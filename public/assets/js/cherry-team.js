( function( $ ) {

	"use strict";

	CherryJsCore.utilites.namespace( 'teamMembersPublic' );
	CherryJsCore.teamMembersPublic = {

		settings: {
			selectors: {
				main: '.team-container',
				filter: '.cherry-team-filter',
				loadMore: '.ajax-more-btn',
				pager: '.team-ajax-pager',
				filterLink: '.cherry-team-filter_link',
				result: '.team-listing',
				container: '.cherry-team',
				loader: '.team-loader',
			},
			actions: {
				filter: 'cherry_team_filter_posts',
				more: 'cherry_team_load_more',
				pager: 'cherry_team_pager'
			},
			templates: {
				loaderLarge: '<a href="#" class="team-loader loader-large">' + window.cherryTeam.loader + '</a>',
				loaderSmall: '<a href="#" class="team-loader loader-small">' + window.cherryTeam.loader + '</a>',
			}
		},

		init: function () {

			var self = this;

			// Document ready event check
			if ( CherryJsCore.status.is_ready ) {
				self.render( self );
			} else {
				CherryJsCore.variable.$document.on( 'ready', self.render( self ) );
			}

		},

		render: function ( self ) {

			var self = self;

			$( self.settings.selectors.main ).each( function() {
				self.initFilters( $( this ), self );
				self.initLoadMore( $( this ), self );
				self.initPager( $( this ), self );
			} );

		},

		addLoader: function( $container, isMore ) {

			var template = this.settings.templates.loaderSmall;

			if ( false === isMore ) {
				$container.addClass( 'in-progress' );
				template = this.settings.templates.loaderLarge;
			}

			$container.append( template );
		},

		removeLoader: function( $container, isMore ) {

			if ( false === isMore ) {
				$container.removeClass( 'in-progress' );
			}

			$container.find( this.settings.selectors.loader ).remove();
		},

		initFilters: function( $item, self ) {

			var $filter    = $item.find( self.settings.selectors.filter ),
				$result    = $item.find( self.settings.selectors.result ),
				$container = $item.find( self.settings.selectors.container ),
				data       = new Object();

			$filter.on( 'click', self.settings.selectors.filterLink, function( event ) {

				var $this   = $( this ),
					$parent = $this.parent();

				event.preventDefault();

				if ( $parent.hasClass( 'active' ) ) {
					return;
				}

				data.group  = $this.data( 'term' );
				data.atts   = $container.data( 'atts' );
				data.action = self.settings.actions.filter;

				$parent.addClass( 'active' ).siblings().removeClass( 'active' );
				self.addLoader( $container, false );

				$.ajax({
					url: window.cherryTeam.ajaxurl,
					type: 'post',
					dataType: 'json',
					data: data,
					error: function() {
						self.removeLoader( $container, false );
					}
				}).done( function( response ) {
					self.removeLoader( $container, false );
					$result.html( response.data.result );
					$container.data( 'atts', response.data.atts );
					$container.data( 'page', 1 );
					$container.data( 'pages', response.data.pages );

					if ( 1 < response.data.pages && $( self.settings.selectors.loadMore ).length ) {
						$( self.settings.selectors.loadMore ).removeClass( 'btn-hidden' );
					}

					if ( 1 == response.data.pages && $( self.settings.selectors.loadMore ).length ) {
						$( self.settings.selectors.loadMore ).addClass( 'btn-hidden' );
					}

					if ( $( self.settings.selectors.pager ).length ) {
						$( self.settings.selectors.pager ).remove();
					}

					$container.append( response.data.pager );

				});
			});
		},

		initLoadMore: function( $item, self ) {

			$item.on( 'click', self.settings.selectors.loadMore, function( event ) {

				var $this      = $( this ),
					$result    = $item.find( self.settings.selectors.result ),
					$container = $item.find( self.settings.selectors.container ),
					pages      = $container.data( 'pages' ),
					data       = new Object();

				event.preventDefault();

				data.page   = $container.data( 'page' );
				data.atts   = $container.data( 'atts' );
				data.action = self.settings.actions.more;

				self.addLoader( $container, true );

				$.ajax({
					url: window.cherryTeam.ajaxurl,
					type: 'post',
					dataType: 'json',
					data: data,
					error: function() {
						self.removeLoader( $container, true );
					}
				}).done( function( response ) {
					self.removeLoader( $container, true );
					$result.append( response.data.result );
					$container.data( 'page', response.data.page );

					if ( response.data.page == pages ) {
						$this.addClass( 'btn-hidden' );
					}

				});

			});

		},

		initPager: function( $item, self ) {

			$item.on( 'click', self.settings.selectors.pager + ' a.page-numbers', function( event ) {

				var $this      = $( this ),
					$result    = $item.find( self.settings.selectors.result ),
					$container = $item.find( self.settings.selectors.container ),
					pages      = $container.data( 'pages' ),
					data       = new Object();

				event.preventDefault();

				data.page   = $this.data( 'page' );
				data.atts   = $container.data( 'atts' );
				data.action = self.settings.actions.pager;

				self.addLoader( $container, false );

				$this.addClass( 'current' ).siblings().removeClass( 'current' );

				$.ajax({
					url: window.cherryTeam.ajaxurl,
					type: 'post',
					dataType: 'json',
					data: data,
					error: function() {
						self.removeLoader( $container, false );
					}
				}).done( function( response ) {

					self.removeLoader( $container, false );
					$result.html( response.data.result );
					$container.data( 'page', response.data.page );

				});

			});

		}

	}

	CherryJsCore.teamMembersPublic.init();

}( jQuery ) );
