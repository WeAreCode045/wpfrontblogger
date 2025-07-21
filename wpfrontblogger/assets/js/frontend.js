/**
 * WP Front Blogger Frontend JavaScript
 */
jQuery(function ($) {
    'use strict';

    // Multi-step form handler
    const WPFrontBlogger = {
        currentStep: 1,
        totalSteps: 3,
        selectedCategories: [],
        selectedTags: [],
        selectedProducts: [],
        newCategories: [],
        newTags: [],
        envatoPage: 1,
        envatoQuery: '',
        selectedEnvatoImage: null,

        init: function() {
            this.bindEvents();
            this.initAutocomplete();
            this.initImageUpload();
            this.initEnvatoElements();
        },

        bindEvents: function() {
            // Step navigation
            $(document).on('click', '.btn-next', this.nextStep.bind(this));
            $(document).on('click', '.btn-prev', this.prevStep.bind(this));
            
            // Form submission
            $(document).on('submit', '#wpfrontblogger-form', this.submitForm.bind(this));
            
            // Remove selected items
            $(document).on('click', '.selected-item .remove', this.removeSelectedItem.bind(this));
            
            // Create another post
            $(document).on('click', '#create-another', this.resetForm.bind(this));
            
            // Visit created post
            $(document).on('click', '#visit-post', this.visitPost.bind(this));
            
            // Image removal
            $(document).on('click', '#remove-image', this.removeImage.bind(this));
            
            // Image source tabs
            $(document).on('click', '.tab-button', this.switchImageTab.bind(this));
            
            // Envato Elements
            $(document).on('click', '#envato-search-btn', this.searchEnvatoImages.bind(this));
            $(document).on('keypress', '#envato-search', this.handleEnvatoSearchKeypress.bind(this));
            $(document).on('click', '.envato-image', this.selectEnvatoImage.bind(this));
            $(document).on('click', '#envato-prev-page', this.envatoPrevPage.bind(this));
            $(document).on('click', '#envato-next-page', this.envatoNextPage.bind(this));
        },

        nextStep: function(e) {
            e.preventDefault();
            const nextStep = parseInt($(e.target).data('next'));
            
            if (this.validateStep(this.currentStep)) {
                this.goToStep(nextStep);
            }
        },

        prevStep: function(e) {
            e.preventDefault();
            const prevStep = parseInt($(e.target).data('prev'));
            this.goToStep(prevStep);
        },

        goToStep: function(step) {
            if (step < 1 || step > this.totalSteps) return;
            
            // Hide current step
            $('.form-step').removeClass('active');
            $('.progress-step').removeClass('active');
            
            // Show target step
            $('#step-' + step).addClass('active');
            $('.progress-step[data-step="' + step + '"]').addClass('active');
            
            // Mark completed steps
            for (let i = 1; i < step; i++) {
                $('.progress-step[data-step="' + i + '"]').addClass('completed');
            }
            
            this.currentStep = step;
            
            // Scroll to top
            $('html, body').animate({
                scrollTop: $('#wpfrontblogger-form-container').offset().top - 50
            }, 500);
        },

        validateStep: function(step) {
            let valid = true;
            $('.field-error').removeClass('show');
            
            switch(step) {
                case 1:
                    const title = $('#post_title').val().trim();
                    if (!title) {
                        $('#post_title_error').text('Title is required').addClass('show');
                        valid = false;
                    }
                    break;
                    
                case 2:
                    const content = this.getEditorContent();
                    if (!content) {
                        $('#post_content_error').text('Content is required').addClass('show');
                        valid = false;
                    }
                    break;
            }
            
            return valid;
        },

        getEditorContent: function() {
            if (typeof tinyMCE !== 'undefined' && tinyMCE.get('post_content')) {
                return tinyMCE.get('post_content').getContent();
            }
            return $('#post_content').val();
        },

        initAutocomplete: function() {
            // Categories autocomplete
            $('#categories').autocomplete({
                source: function(request, response) {
                    WPFrontBlogger.searchItems('categories', request.term, response);
                },
                select: function(event, ui) {
                    WPFrontBlogger.addCategory(ui.item);
                    $(this).val('');
                    return false;
                },
                minLength: 2
            }).keypress(function(e) {
                if (e.which === 13) { // Enter key
                    e.preventDefault();
                    const value = $(this).val().trim();
                    if (value) {
                        WPFrontBlogger.addNewCategory(value);
                        $(this).val('');
                    }
                }
            });

            // Tags autocomplete
            $('#tags').autocomplete({
                source: function(request, response) {
                    WPFrontBlogger.searchItems('tags', request.term, response);
                },
                select: function(event, ui) {
                    WPFrontBlogger.addTag(ui.item);
                    $(this).val('');
                    return false;
                },
                minLength: 2
            }).keypress(function(e) {
                if (e.which === 13) { // Enter key
                    e.preventDefault();
                    const value = $(this).val().trim();
                    if (value) {
                        WPFrontBlogger.addNewTag(value);
                        $(this).val('');
                    }
                }
            });

            // Products autocomplete (if WooCommerce is active)
            $('#related_products').autocomplete({
                source: function(request, response) {
                    WPFrontBlogger.searchItems('products', request.term, response);
                },
                select: function(event, ui) {
                    WPFrontBlogger.addProduct(ui.item);
                    $(this).val('');
                    return false;
                },
                minLength: 2
            });
        },

        searchItems: function(type, term, callback) {
            $.ajax({
                url: wpfrontblogger_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'wpfrontblogger_search_' + type,
                    term: term,
                    nonce: wpfrontblogger_ajax.nonce
                },
                success: function(response) {
                    callback(response);
                },
                error: function() {
                    callback([]);
                }
            });
        },

        addCategory: function(item) {
            if (!this.selectedCategories.find(cat => cat.id === item.id)) {
                this.selectedCategories.push(item);
                this.renderSelectedItems('categories');
            }
        },

        addNewCategory: function(name) {
            if (!this.newCategories.includes(name) && 
                !this.selectedCategories.find(cat => cat.label === name)) {
                this.newCategories.push(name);
                this.selectedCategories.push({id: 'new-' + Date.now(), label: name, isNew: true});
                this.renderSelectedItems('categories');
            }
        },

        addTag: function(item) {
            if (!this.selectedTags.find(tag => tag.id === item.id)) {
                this.selectedTags.push(item);
                this.renderSelectedItems('tags');
            }
        },

        addNewTag: function(name) {
            if (!this.newTags.includes(name) && 
                !this.selectedTags.find(tag => tag.label === name)) {
                this.newTags.push(name);
                this.selectedTags.push({id: 'new-' + Date.now(), label: name, isNew: true});
                this.renderSelectedItems('tags');
            }
        },

        addProduct: function(item) {
            if (!this.selectedProducts.find(product => product.id === item.id)) {
                this.selectedProducts.push(item);
                this.renderSelectedItems('products');
            }
        },

        renderSelectedItems: function(type) {
            let items, container;
            
            switch(type) {
                case 'categories':
                    items = this.selectedCategories;
                    container = '#selected-categories';
                    break;
                case 'tags':
                    items = this.selectedTags;
                    container = '#selected-tags';
                    break;
                case 'products':
                    items = this.selectedProducts;
                    container = '#selected-products';
                    break;
            }

            const html = items.map(item => 
                `<span class="selected-item" data-type="${type}" data-id="${item.id}">
                    ${item.label} ${item.isNew ? '(new)' : ''}
                    <span class="remove">×</span>
                </span>`
            ).join('');

            $(container).html(html);
        },

        removeSelectedItem: function(e) {
            const $item = $(e.target).closest('.selected-item');
            const type = $item.data('type');
            const id = $item.data('id');

            switch(type) {
                case 'categories':
                    this.selectedCategories = this.selectedCategories.filter(cat => cat.id != id);
                    if (id.toString().startsWith('new-')) {
                        const label = $item.text().replace(' (new)', '').replace('×', '').trim();
                        this.newCategories = this.newCategories.filter(name => name !== label);
                    }
                    this.renderSelectedItems('categories');
                    break;
                case 'tags':
                    this.selectedTags = this.selectedTags.filter(tag => tag.id != id);
                    if (id.toString().startsWith('new-')) {
                        const label = $item.text().replace(' (new)', '').replace('×', '').trim();
                        this.newTags = this.newTags.filter(name => name !== label);
                    }
                    this.renderSelectedItems('tags');
                    break;
                case 'products':
                    this.selectedProducts = this.selectedProducts.filter(product => product.id != id);
                    this.renderSelectedItems('products');
                    break;
            }
        },

        initImageUpload: function() {
            const $fileInput = $('#featured_image');
            const $preview = $('#image-preview');
            const $placeholder = $('#upload-placeholder');
            const $previewImg = $('#preview-img');

            $fileInput.on('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // Show preview
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $previewImg.attr('src', e.target.result);
                        $placeholder.hide();
                        $preview.show();
                    };
                    reader.readAsDataURL(file);

                    // Upload file
                    WPFrontBlogger.uploadImage(file);
                }
            });
        },

        uploadImage: function(file) {
            const formData = new FormData();
            formData.append('featured_image', file);
            formData.append('action', 'wpfrontblogger_upload_image');
            formData.append('nonce', wpfrontblogger_ajax.nonce);

            $.ajax({
                url: wpfrontblogger_ajax.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        $('#featured_image_id').val(response.data.attachment_id);
                    } else {
                        alert('Error uploading image: ' + response.data);
                    }
                },
                error: function() {
                    alert('Error uploading image. Please try again.');
                }
            });
        },

        removeImage: function(e) {
            e.preventDefault();
            $('#image-preview').hide();
            $('#upload-placeholder').show();
            $('#featured_image').val('');
            $('#featured_image_id').val('');
        },

        switchImageTab: function(e) {
            e.preventDefault();
            const tab = $(e.target).data('tab');
            
            // Update tab buttons
            $('.tab-button').removeClass('active');
            $(e.target).addClass('active');
            
            // Update tab content
            $('.image-tab-content').removeClass('active');
            $('#' + tab + '-tab').addClass('active');
            
            // Update hidden field
            $('#featured_image_source').val(tab);
            
            // Clear previous selections when switching tabs
            this.clearImageSelection();
        },

        clearImageSelection: function() {
            $('#featured_image_id').val('');
            $('#image-preview').hide();
            $('#upload-placeholder').show();
            $('#featured_image').val('');
            this.selectedEnvatoImage = null;
            $('.envato-image').removeClass('selected');
        },

        initEnvatoElements: function() {
            // Initialize Envato Elements functionality if the tab exists
            if ($('#envato-tab').length === 0) return;
            
            // Set up initial state
            this.envatoPage = 1;
            this.envatoQuery = '';
        },

        handleEnvatoSearchKeypress: function(e) {
            if (e.which === 13) { // Enter key
                e.preventDefault();
                this.searchEnvatoImages();
            }
        },

        searchEnvatoImages: function(page = 1) {
            const query = $('#envato-search').val().trim();
            if (!query) return;
            
            this.envatoQuery = query;
            this.envatoPage = page;
            
            $('#envato-loading').show();
            $('#envato-results').empty();
            $('#envato-pagination').hide();
            
            $.ajax({
                url: wpfrontblogger_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'wpfrontblogger_search_envato_images',
                    term: query,
                    page: page,
                    per_page: 12,
                    nonce: wpfrontblogger_ajax.nonce
                },
                success: (response) => {
                    $('#envato-loading').hide();
                    
                    if (response.success && response.data.images) {
                        this.renderEnvatoImages(response.data.images);
                        this.updateEnvatoPagination(response.data);
                    } else {
                        $('#envato-results').html('<p class="envato-error">No images found or Envato Elements not properly configured.</p>');
                    }
                },
                error: () => {
                    $('#envato-loading').hide();
                    $('#envato-results').html('<p class="envato-error">Error searching images. Please try again.</p>');
                }
            });
        },

        renderEnvatoImages: function(images) {
            const html = images.map(image => `
                <div class="envato-image" data-id="${image.id}" data-url="${image.preview}" data-title="${image.title}">
                    <img src="${image.thumbnail}" alt="${image.title}" loading="lazy">
                    <div class="envato-image-info">
                        <div class="envato-image-title">${image.title}</div>
                        <div class="envato-image-author">by ${image.author}</div>
                    </div>
                </div>
            `).join('');
            
            $('#envato-results').html(html);
        },

        updateEnvatoPagination: function(data) {
            if (data.total > 12) { // Show pagination if more than one page
                $('#envato-page-info').text(`Page ${this.envatoPage}`);
                
                // Update previous button
                if (this.envatoPage > 1) {
                    $('#envato-prev-page').prop('disabled', false);
                } else {
                    $('#envato-prev-page').prop('disabled', true);
                }
                
                // Update next button (simplified - in real implementation, you'd want total pages)
                if (data.images.length === 12) {
                    $('#envato-next-page').prop('disabled', false);
                } else {
                    $('#envato-next-page').prop('disabled', true);
                }
                
                $('#envato-pagination').show();
            }
        },

        envatoPrevPage: function(e) {
            e.preventDefault();
            if (this.envatoPage > 1) {
                this.searchEnvatoImages(this.envatoPage - 1);
            }
        },

        envatoNextPage: function(e) {
            e.preventDefault();
            this.searchEnvatoImages(this.envatoPage + 1);
        },

        selectEnvatoImage: function(e) {
            const $image = $(e.currentTarget);
            
            // Remove previous selection
            $('.envato-image').removeClass('selected');
            
            // Select current image
            $image.addClass('selected importing');
            
            const imageData = {
                id: $image.data('id'),
                url: $image.data('url'),
                title: $image.data('title')
            };
            
            // Import image to WordPress
            this.importEnvatoImage(imageData, $image);
        },

        importEnvatoImage: function(imageData, $imageElement) {
            $.ajax({
                url: wpfrontblogger_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'wpfrontblogger_import_envato_image',
                    image_id: imageData.id,
                    image_url: imageData.url,
                    image_title: imageData.title,
                    nonce: wpfrontblogger_ajax.nonce
                },
                success: (response) => {
                    $imageElement.removeClass('importing');
                    
                    if (response.success) {
                        // Set as featured image
                        $('#featured_image_id').val(response.data.attachment_id);
                        this.selectedEnvatoImage = imageData;
                        
                        // Show success feedback
                        this.showEnvatoImageSelected(response.data);
                    } else {
                        $imageElement.removeClass('selected');
                        alert('Error importing image: ' + response.data);
                    }
                },
                error: () => {
                    $imageElement.removeClass('importing selected');
                    alert('Error importing image. Please try again.');
                }
            });
        },

        showEnvatoImageSelected: function(data) {
            // You could show a preview or confirmation here
            // For now, we'll just indicate success
            const $selectedImage = $('.envato-image.selected');
            $selectedImage.append('<div class="selection-indicator">✓</div>');
            
            setTimeout(() => {
                $('.selection-indicator').fadeOut();
            }, 2000);
        },

        submitForm: function(e) {
            e.preventDefault();
            
            if (!this.validateStep(3)) return;

            // Show loading
            $('#loading-overlay').show();

            const formData = {
                action: 'wpfrontblogger_submit_post',
                post_title: $('#post_title').val(),
                post_content: this.getEditorContent(),
                category_ids: this.selectedCategories.filter(cat => !cat.isNew).map(cat => cat.id),
                new_categories: this.newCategories,
                tag_names: this.selectedTags.filter(tag => !tag.isNew).map(tag => tag.label),
                new_tags: this.newTags,
                product_ids: this.selectedProducts.map(product => product.id),
                featured_image_id: $('#featured_image_id').val(),
                nonce: wpfrontblogger_ajax.nonce
            };

            $.ajax({
                url: wpfrontblogger_ajax.ajax_url,
                type: 'POST',
                data: formData,
                success: function(response) {
                    $('#loading-overlay').hide();
                    
                    if (response.success) {
                        $('#wpfrontblogger-form').hide();
                        $('#success-text').text(response.data.message);
                        
                        // Store the post URL for the visit button
                        $('#created_post_url').val(response.data.post_url);
                        
                        // Show success message with action buttons
                        $('#success-message').show();
                    } else {
                        alert('Error: ' + response.data);
                    }
                },
                error: function() {
                    $('#loading-overlay').hide();
                    alert('An error occurred. Please try again.');
                }
            });
        },

        resetForm: function(e) {
            e.preventDefault();
            
            // Reset form
            $('#wpfrontblogger-form')[0].reset();
            
            // Reset variables
            this.selectedCategories = [];
            this.selectedTags = [];
            this.selectedProducts = [];
            this.newCategories = [];
            this.newTags = [];
            
            // Clear selected items
            $('.selected-items').empty();
            
            // Reset image upload
            $('#image-preview').hide();
            $('#upload-placeholder').show();
            $('#featured_image_id').val('');
            
            // Reset editor
            if (typeof tinyMCE !== 'undefined' && tinyMCE.get('post_content')) {
                tinyMCE.get('post_content').setContent('');
            }
            
            // Hide success message and show form
            $('#success-message').hide();
            $('#wpfrontblogger-form').show();
            
            // Go to first step
            this.goToStep(1);
        },

        visitPost: function(e) {
            e.preventDefault();
            
            const postUrl = $('#created_post_url').val();
            if (postUrl) {
                window.open(postUrl, '_blank');
            } else {
                alert('Post URL not available');
            }
        }
    };

    // Initialize when document is ready
    WPFrontBlogger.init();
});