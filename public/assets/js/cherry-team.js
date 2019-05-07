( function( $ ) {

	'use strict';

	var teamMembersPublic = {

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
			},
			state: {
				filters: false,
				more: false,
				pager: false
			}
		},

		init: function () {
			var self = this;
			self.render( self );
		},

		render: function ( self ) {

			$( self.settings.selectors.main ).each( function() {
				var $this = $( this );

				if ( ! $this.data( 'inited' ) ) {
					$this.data( 'inited', 'true' );

					self.initFilters( $( this ) );
					self.initLoadMore( $( this ) );
					self.initPager( $( this ) );
				}
			} );

			$( window ).on( 'elementor/frontend/init', self.initElementorWidget );

		},

		initElementorWidget: function() {

			window.elementorFrontend.hooks.addAction(
				'frontend/element_ready/cherry_team.default',
				function( $scope ) {

					var $container = $scope.find( teamMembersPublic.settings.selectors.main );

					if ( $container.length ) {

						if ( ! $container.data( 'inited' ) ) {
							$container.data( 'inited', 'true' );

							teamMembersPublic.initFilters( $container );
							teamMembersPublic.initLoadMore( $container );
							teamMembersPublic.initPager( $container );
						}

					}

				}
			);

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

		initFilters: function( $item ) {

			var $filter    = $item.find( teamMembersPublic.settings.selectors.filter ),
				$result    = $item.find( teamMembersPublic.settings.selectors.result ),
				$container = $item.find( teamMembersPublic.settings.selectors.container ),
				data       = {};

			$filter.on( 'click', teamMembersPublic.settings.selectors.filterLink, function( event ) {

				var $this   = $( this ),
					$parent = $this.parent();

				event.preventDefault();

				if ( $parent.hasClass( 'active' ) ) {
					return;
				}

				data.group  = $this.data( 'term' );
				data.atts   = $container.data( 'atts' );
				data.groups = $container.data( 'groups' );
				data.action = teamMembersPublic.settings.actions.filter;

				$parent.addClass( 'active' ).siblings().removeClass( 'active' );
				teamMembersPublic.addLoader( $container, false );

				$.ajax({
					url: window.cherryTeam.ajaxurl,
					type: 'post',
					dataType: 'json',
					data: data,
					error: function() {
						teamMembersPublic.removeLoader( $container, false );
					}
				}).done( function( response ) {
					teamMembersPublic.removeLoader( $container, false );
					$result.html( response.data.result );
					$container.data( 'atts', response.data.atts );
					$container.data( 'page', 1 );
					$container.data( 'pages', response.data.pages );

					if ( 1 < response.data.pages && $( teamMembersPublic.settings.selectors.loadMore ).length ) {
						$( teamMembersPublic.settings.selectors.loadMore ).removeClass( 'btn-hidden' );
					}

					if ( 1 == response.data.pages && $( teamMembersPublic.settings.selectors.loadMore ).length ) {
						$( teamMembersPublic.settings.selectors.loadMore ).addClass( 'btn-hidden' );
					}

					if ( $( teamMembersPublic.settings.selectors.pager ).length ) {
						$( teamMembersPublic.settings.selectors.pager ).remove();
					}

					$container.append( response.data.pager );

				});
			});
		},

		initLoadMore: function( $item ) {

			$item.on( 'click', teamMembersPublic.settings.selectors.loadMore, function( event ) {

				var $this      = $( this ),
					$result    = $item.find( teamMembersPublic.settings.selectors.result ),
					$container = $item.find( teamMembersPublic.settings.selectors.container ),
					pages      = $container.data( 'pages' ),
					data       = {};

				event.preventDefault();

				data.page   = $container.data( 'page' );
				data.atts   = $container.data( 'atts' );
				data.action = teamMembersPublic.settings.actions.more;

				teamMembersPublic.addLoader( $container, true );

				$.ajax({
					url: window.cherryTeam.ajaxurl,
					type: 'post',
					dataType: 'json',
					data: data,
					error: function() {
						teamMembersPublic.removeLoader( $container, true );
					}
				}).done( function( response ) {
					teamMembersPublic.removeLoader( $container, true );
					$result.append( response.data.result );
					$container.data( 'page', response.data.page );

					if ( response.data.page == pages ) {
						$this.addClass( 'btn-hidden' );
					}

				});

			});

		},

		initPager: function( $item ) {

			$item.on( 'click', teamMembersPublic.settings.selectors.pager + ' a.page-numbers', function( event ) {

				var $this      = $( this ),
					$result    = $item.find( teamMembersPublic.settings.selectors.result ),
					$container = $item.find( teamMembersPublic.settings.selectors.container ),
					pages      = $container.data( 'pages' ),
					data       = {};

				event.preventDefault();

				data.page   = $this.data( 'page' );
				data.atts   = $container.data( 'atts' );
				data.action = teamMembersPublic.settings.actions.pager;

				teamMembersPublic.addLoader( $container, false );

				$this.addClass( 'current' ).siblings().removeClass( 'current' );

				$.ajax({
					url: window.cherryTeam.ajaxurl,
					type: 'post',
					dataType: 'json',
					data: data,
					error: function() {
						teamMembersPublic.removeLoader( $container, false );
					}
				}).done( function( response ) {

					teamMembersPublic.removeLoader( $container, false );
					$result.html( response.data.result );
					$container.data( 'page', response.data.page );

				});

			});

		}

	}

	teamMembersPublic.init();

}( jQuery ) );
