(function($) {

	FLBuilderPostGrid = function(settings)
	{
		this.settings    = settings;
		this.nodeClass   = '.fl-node-' + settings.id;
		this.matchHeight = settings.matchHeight;

		this.wrapperClass = this.nodeClass + ' .fl-post-' + this.settings.layout;
		this.postClass    = this.wrapperClass + '-post';
		
		this._initLayout();

	};

	FLBuilderPostGrid.prototype = {

		settings        : {},
		nodeClass       : '',
		wrapperClass    : '',
		postClass       : '',
		gallery         : null,

		_initLayout: function()
		{
			this._galleryLayout();

			$(this.postClass).css('visibility', 'visible');

			FLBuilderLayout._scrollToElement( $( this.nodeClass + ' .fl-paged-scroll-to' ) );
		},

		_galleryLayout: function()
		{
			this.gallery = new FLBuilderGalleryGrid({
				'wrapSelector' : this.wrapperClass,
				'itemSelector' : '.fl-post-gallery-post',
				'isRTL'        : this.settings.isRTL
			});
		},
 


	};

})(jQuery);
