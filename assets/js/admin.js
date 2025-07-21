/**
 * WP Front Blogger - Admin JavaScript
 * Adapted from frontend functionality for WordPress admin interface
 */
jQuery(document).ready(function($) {
    'use strict';
    
    let currentStep = 1;
    let totalSteps = 3;
    let selectedCategories = [];
    let selectedTags = [];
    let selectedProducts = [];
    let envatoCurrentPage = 1;
    let envatoTotalPages = 1;
    let envatoCurrentQuery = '';
    
    // Initialize the form
    init();
    
    function init() {
        setupStepNavigation();
        setupAutocomplete();
        setupImageUpload();
        setupEnvatoElements();
        setupFormSubmission();
        setupPostSubmissionActions();
        setupAIHandlers(); // Add AI functionality
        
        // WordPress editor integration
        if (typeof tinymce !== 'undefined') {
            $(document).on('tinymce-editor-init', function(event, editor) {
                if (editor.id === 'post_content') {
                    // Editor is ready
                }
            });
        }
    }
    
    // Step navigation
    function setupStepNavigation() {
        $('.btn-next').on('click', function() {
            const nextStep = parseInt($(this).data('next'));
            if (validateStep(currentStep)) {
                goToStep(nextStep);
            }
        });
        
        $('.btn-prev').on('click', function() {
            const prevStep = parseInt($(this).data('prev'));
            goToStep(prevStep);
        });
    }
    
    function goToStep(step) {
        if (step < 1 || step > totalSteps) return;
        
        // Hide current step
        $('.form-step').removeClass('active');
        $('.progress-step').removeClass('active');
        
        // Show target step
        $('#step-' + step).addClass('active');
        $('.progress-step[data-step="' + step + '"]').addClass('active');
        
        // Mark completed steps
        $('.progress-step').each(function() {
            const stepNum = parseInt($(this).data('step'));
            if (stepNum < step) {
                $(this).addClass('completed');
            } else {
                $(this).removeClass('completed');
            }
        });
        
        currentStep = step;
        
        // Focus on first input of new step
        setTimeout(function() {
            $('#step-' + step + ' input[type="text"]:first').focus();
        }, 100);
    }
    
    // Form validation
    function validateStep(step) {
        let isValid = true;
        
        // Clear previous errors
        $('.field-error').removeClass('show').text('');
        
        switch(step) {
            case 1:
                const title = $('#post_title').val().trim();
                if (!title) {
                    showFieldError('post_title', 'Title is required');
                    isValid = false;
                }
                break;
                
            case 2:
                const content = getEditorContent();
                if (!content || content.trim() === '') {
                    showFieldError('post_content', 'Content is required');
                    isValid = false;
                }
                break;
        }
        
        return isValid;
    }
    
    function showFieldError(fieldId, message) {
        $('#' + fieldId + '_error').text(message).addClass('show');
        $('#' + fieldId).focus();
    }
    
    function getEditorContent() {
        if (typeof tinymce !== 'undefined' && tinymce.get('post_content')) {
            return tinymce.get('post_content').getContent();
        }
        return $('#post_content').val();
    }
    
    // Autocomplete setup
    function setupAutocomplete() {
        // Categories autocomplete
        $('#categories').autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: wpfrontblogger_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'wpfrontblogger_search_categories',
                        term: request.term,
                        nonce: wpfrontblogger_ajax.nonce
                    },
                    success: function(data) {
                        if (data.success) {
                            response(data.data);
                        } else {
                            response([]);
                        }
                    }
                });
            },
            minLength: 2,
            select: function(event, ui) {
                addSelectedItem('categories', ui.item.id, ui.item.label);
                $(this).val('');
                return false;
            }
        }).on('keypress', function(e) {
            if (e.which === 13) { // Enter key
                const value = $(this).val().trim();
                if (value) {
                    addSelectedItem('categories', 'new:' + value, value, true);
                    $(this).val('');
                }
                e.preventDefault();
            }
        });
        
        // Tags autocomplete
        $('#tags').autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: wpfrontblogger_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'wpfrontblogger_search_tags',
                        term: request.term,
                        nonce: wpfrontblogger_ajax.nonce
                    },
                    success: function(data) {
                        if (data.success) {
                            response(data.data);
                        } else {
                            response([]);
                        }
                    }
                });
            },
            minLength: 2,
            select: function(event, ui) {
                addSelectedItem('tags', ui.item.id, ui.item.label);
                $(this).val('');
                return false;
            }
        }).on('keypress', function(e) {
            if (e.which === 13) { // Enter key
                const value = $(this).val().trim();
                if (value) {
                    addSelectedItem('tags', 'new:' + value, value, true);
                    $(this).val('');
                }
                e.preventDefault();
            }
        });
        
        // Products autocomplete (WooCommerce)
        $('#related_products').autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: wpfrontblogger_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'wpfrontblogger_search_products',
                        term: request.term,
                        nonce: wpfrontblogger_ajax.nonce
                    },
                    success: function(data) {
                        if (data.success) {
                            response(data.data);
                        } else {
                            response([]);
                        }
                    }
                });
            },
            minLength: 3,
            select: function(event, ui) {
                addSelectedItem('products', ui.item.id, ui.item.label);
                $(this).val('');
                return false;
            }
        });
    }
    
    function addSelectedItem(type, id, label, isNew = false) {
        const containerId = 'selected-' + type;
        let selectedArray;
        
        switch(type) {
            case 'categories':
                selectedArray = selectedCategories;
                break;
            case 'tags':
                selectedArray = selectedTags;
                break;
            case 'products':
                selectedArray = selectedProducts;
                break;
        }
        
        // Check if already selected
        if (selectedArray.some(item => item.id === id)) {
            return;
        }
        
        selectedArray.push({ id: id, label: label, isNew: isNew });
        
        const selectedItem = $('<div class="selected-item">' +
            '<span class="item-label">' + label + '</span>' +
            '<button type="button" class="remove-item" data-type="' + type + '" data-id="' + id + '">&times;</button>' +
        '</div>');
        
        $('#' + containerId).append(selectedItem);
        updateHiddenFields();
    }
    
    $(document).on('click', '.remove-item', function() {
        const type = $(this).data('type');
        const id = $(this).data('id');
        
        let selectedArray;
        switch(type) {
            case 'categories':
                selectedArray = selectedCategories;
                break;
            case 'tags':
                selectedArray = selectedTags;
                break;
            case 'products':
                selectedArray = selectedProducts;
                break;
        }
        
        // Remove from array
        const index = selectedArray.findIndex(item => item.id === id);
        if (index > -1) {
            selectedArray.splice(index, 1);
        }
        
        // Remove from DOM
        $(this).closest('.selected-item').remove();
        updateHiddenFields();
    });
    
    function updateHiddenFields() {
        // Update category fields
        const existingCats = selectedCategories.filter(cat => !cat.isNew).map(cat => cat.id);
        const newCats = selectedCategories.filter(cat => cat.isNew).map(cat => cat.label);
        $('#selected_category_ids').val(existingCats.join(','));
        $('#new_categories').val(newCats.join(','));
        
        // Update tag fields
        const newTagNames = selectedTags.map(tag => tag.label);
        $('#selected_tag_names').val(newTagNames.join(','));
        $('#new_tags').val(newTagNames.join(','));
        
        // Update product fields
        const productIds = selectedProducts.map(product => product.id);
        $('#selected_product_ids').val(productIds.join(','));
    }
    
    // Image upload setup
    function setupImageUpload() {
        // Tab switching
        $('.tab-button').on('click', function() {
            const tab = $(this).data('tab');
            
            $('.tab-button').removeClass('active');
            $('.image-tab-content').removeClass('active');
            
            $(this).addClass('active');
            $('#' + tab + '-tab').addClass('active');
            
            $('#featured_image_source').val(tab);
        });
        
        // File input handling
        $('#featured_image').on('change', function() {
            const file = this.files[0];
            if (file) {
                // Validate file
                if (!file.type.startsWith('image/')) {
                    alert('Please select a valid image file.');
                    return;
                }
                
                if (file.size > 5 * 1024 * 1024) { // 5MB limit
                    alert('Please select an image smaller than 5MB.');
                    return;
                }
                
                // Preview image
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#preview-img').attr('src', e.target.result);
                    $('#image-preview').show();
                    $('#upload-placeholder').hide();
                };
                reader.readAsDataURL(file);
            }
        });
        
        // Remove image
        $('#remove-image').on('click', function() {
            $('#featured_image').val('');
            $('#image-preview').hide();
            $('#upload-placeholder').show();
            $('#featured_image_id').val('');
        });
        
        // Drag and drop
        const uploadContainer = $('.image-upload-container');
        
        uploadContainer.on('dragover dragenter', function(e) {
            e.preventDefault();
            $(this).addClass('drag-over');
        });
        
        uploadContainer.on('dragleave dragend drop', function(e) {
            e.preventDefault();
            $(this).removeClass('drag-over');
        });
        
        uploadContainer.on('drop', function(e) {
            const files = e.originalEvent.dataTransfer.files;
            if (files.length > 0) {
                $('#featured_image')[0].files = files;
                $('#featured_image').trigger('change');
            }
        });
    }
    
    // Envato Elements integration
    function setupEnvatoElements() {
        $('#envato-search-btn').on('click', function() {
            const query = $('#envato-search').val().trim();
            if (query) {
                searchEnvatoImages(query, 1);
            }
        });
        
        $('#envato-search').on('keypress', function(e) {
            if (e.which === 13) {
                const query = $(this).val().trim();
                if (query) {
                    searchEnvatoImages(query, 1);
                }
                e.preventDefault();
            }
        });
        
        // Pagination
        $('#envato-prev-page').on('click', function() {
            if (envatoCurrentPage > 1) {
                searchEnvatoImages(envatoCurrentQuery, envatoCurrentPage - 1);
            }
        });
        
        $('#envato-next-page').on('click', function() {
            if (envatoCurrentPage < envatoTotalPages) {
                searchEnvatoImages(envatoCurrentQuery, envatoCurrentPage + 1);
            }
        });
    }
    
    function searchEnvatoImages(query, page = 1) {
        envatoCurrentQuery = query;
        envatoCurrentPage = page;
        
        $('#envato-loading').show();
        $('#envato-results').hide();
        
        $.ajax({
            url: wpfrontblogger_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'wpfrontblogger_search_envato_images',
                query: query,
                page: page,
                nonce: wpfrontblogger_ajax.nonce
            },
            success: function(response) {
                $('#envato-loading').hide();
                
                if (response.success && response.data) {
                    displayEnvatoResults(response.data);
                } else {
                    $('#envato-results').html('<p>No images found. Try a different search term.</p>').show();
                }
            },
            error: function() {
                $('#envato-loading').hide();
                $('#envato-results').html('<p>Error searching images. Please try again.</p>').show();
            }
        });
    }
    
    function displayEnvatoResults(data) {
        const resultsHtml = data.images.map(image => 
            '<div class="envato-image" data-image-id="' + image.id + '" data-image-url="' + image.url + '">' +
                '<img src="' + image.thumbnail + '" alt="' + image.title + '">' +
                '<div class="image-info">' +
                    '<div class="image-title">' + image.title + '</div>' +
                '</div>' +
            '</div>'
        ).join('');
        
        $('#envato-results').html(resultsHtml).show();
        
        // Update pagination
        envatoTotalPages = data.total_pages || 1;
        updateEnvatoPagination();
        
        // Handle image selection
        $('.envato-image').on('click', function() {
            $('.envato-image').removeClass('selected');
            $(this).addClass('selected');
            
            const imageId = $(this).data('image-id');
            const imageUrl = $(this).data('image-url');
            
            importEnvatoImage(imageId, imageUrl);
        });
    }
    
    function updateEnvatoPagination() {
        $('#envato-page-info').text('Page ' + envatoCurrentPage + ' of ' + envatoTotalPages);
        
        $('#envato-prev-page').prop('disabled', envatoCurrentPage <= 1);
        $('#envato-next-page').prop('disabled', envatoCurrentPage >= envatoTotalPages);
        
        $('#envato-pagination').toggle(envatoTotalPages > 1);
    }
    
    function importEnvatoImage(imageId, imageUrl) {
        $.ajax({
            url: wpfrontblogger_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'wpfrontblogger_import_envato_image',
                image_id: imageId,
                image_url: imageUrl,
                nonce: wpfrontblogger_ajax.nonce
            },
            success: function(response) {
                if (response.success && response.data) {
                    $('#featured_image_id').val(response.data.attachment_id);
                    
                    // Show preview
                    $('#preview-img').attr('src', response.data.url);
                    $('#image-preview').show();
                    $('#upload-placeholder').hide();
                    
                    // Show success message briefly
                    showNotice('Image imported successfully!', 'success');
                } else {
                    showNotice('Failed to import image. Please try another one.', 'error');
                }
            },
            error: function() {
                showNotice('Error importing image. Please try again.', 'error');
            }
        });
    }
    
    // Form submission
    function setupFormSubmission() {
        $('#wpfrontblogger-admin-form').on('submit', function(e) {
            e.preventDefault();
            
            if (!validateStep(currentStep) || !validateAllSteps()) {
                return;
            }
            
            submitForm();
        });
    }
    
    function validateAllSteps() {
        let isValid = true;
        
        // Validate all required fields
        const title = $('#post_title').val().trim();
        const content = getEditorContent();
        
        if (!title || !content || content.trim() === '') {
            showNotice('Please complete all required fields before submitting.', 'error');
            isValid = false;
        }
        
        return isValid;
    }
    
    function submitForm() {
        $('#loading-overlay').show();
        
        // Prepare form data
        const formData = new FormData();
        formData.append('action', 'wpfrontblogger_submit_post');
        formData.append('nonce', wpfrontblogger_ajax.nonce);
        formData.append('post_title', $('#post_title').val());
        formData.append('post_content', getEditorContent());
        formData.append('selected_category_ids', $('#selected_category_ids').val());
        formData.append('selected_tag_names', $('#selected_tag_names').val());
        formData.append('selected_product_ids', $('#selected_product_ids').val());
        formData.append('new_categories', $('#new_categories').val());
        formData.append('new_tags', $('#new_tags').val());
        formData.append('featured_image_id', $('#featured_image_id').val());
        formData.append('featured_image_source', $('#featured_image_source').val());
        
        // Add file if uploaded
        const fileInput = $('#featured_image')[0];
        if (fileInput.files.length > 0) {
            formData.append('featured_image', fileInput.files[0]);
        }
        
        $.ajax({
            url: wpfrontblogger_ajax.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#loading-overlay').hide();
                
                if (response.success && response.data) {
                    showSuccessMessage(response.data);
                } else {
                    const message = response.data && response.data.message ? 
                        response.data.message : 
                        'Failed to create blog post. Please try again.';
                    showNotice(message, 'error');
                }
            },
            error: function() {
                $('#loading-overlay').hide();
                showNotice('Error submitting form. Please try again.', 'error');
            }
        });
    }
    
    function showSuccessMessage(data) {
        $('#wpfrontblogger-admin-form').hide();
        $('#success-text').text(data.message);
        $('#visit-post').attr('href', data.post_url);
        $('#created_post_url').val(data.post_url);
        $('#success-message').show();
        
        // Scroll to success message
        $('html, body').animate({
            scrollTop: $('#success-message').offset().top - 100
        }, 500);
    }
    
    // Post-submission actions
    function setupPostSubmissionActions() {
        $('#create-another').on('click', function() {
            // Reset form
            resetForm();
            $('#success-message').hide();
            $('#wpfrontblogger-admin-form').show();
            goToStep(1);
            
            // Scroll to top
            $('html, body').animate({
                scrollTop: $('#wpfrontblogger-admin-container').offset().top - 100
            }, 500);
        });
    }
    
    function resetForm() {
        // Clear form fields
        $('#wpfrontblogger-admin-form')[0].reset();
        
        // Clear editor
        if (typeof tinymce !== 'undefined' && tinymce.get('post_content')) {
            tinymce.get('post_content').setContent('');
        }
        
        // Clear selected items
        selectedCategories = [];
        selectedTags = [];
        selectedProducts = [];
        $('.selected-items').empty();
        
        // Reset image
        $('#image-preview').hide();
        $('#upload-placeholder').show();
        
        // Clear hidden fields
        updateHiddenFields();
        
        // Reset Envato
        $('#envato-results').empty();
        $('#envato-search').val('');
        
        // Clear errors
        $('.field-error').removeClass('show').text('');
        
        // Reset tabs
        $('.tab-button').removeClass('active').first().addClass('active');
        $('.image-tab-content').removeClass('active').first().addClass('active');
        $('#featured_image_source').val('upload');
    }
    
    // Utility functions
    function showNotice(message, type = 'info') {
        const noticeClass = 'notice-' + type;
        const notice = $('<div class="notice ' + noticeClass + ' is-dismissible">' +
            '<p>' + message + '</p>' +
            '<button type="button" class="notice-dismiss">' +
                '<span class="screen-reader-text">Dismiss this notice.</span>' +
            '</button>' +
        '</div>');
        
        $('.wrap h1').after(notice);
        
        // Auto-dismiss after 5 seconds for success messages
        if (type === 'success') {
            setTimeout(function() {
                notice.fadeOut(function() {
                    notice.remove();
                });
            }, 5000);
        }
        
        // Handle dismiss button
        notice.find('.notice-dismiss').on('click', function() {
            notice.fadeOut(function() {
                notice.remove();
            });
        });
    }
    
    // ========================================
    // AI FUNCTIONALITY
    // ========================================
    
    // Setup AI event handlers
    function setupAIHandlers() {
        // AI Generate Title
        $('#ai-generate-title').on('click', handleAIGenerateTitle);
        
        // AI Rewrite Content
        $('#ai-rewrite-content').on('click', handleAIRewriteContent);
        
        // AI Select Categories
        $('#ai-select-categories').on('click', handleAISelectCategories);
        
        // AI Generate Tags
        $('#ai-generate-tags').on('click', handleAIGenerateTags);
        
        // AI Select Products
        $('#ai-select-products').on('click', handleAISelectProducts);
        
        // AI Find Image
        $('#ai-find-image').on('click', handleAIFindImage);
        
        // AI image tab functionality
        $('.tab-button[data-tab="ai-image"]').on('click', function() {
            switchImageTab('ai-image');
        });
        
        // Use AI selected image
        $('#use-ai-image').on('click', handleUseAIImage);
        
        // Try AI image again
        $('#try-ai-again').on('click', function() {
            $('#ai-image-results, .ai-image-actions').hide();
            $('#ai-find-image').show();
        });
        
        // Handle title suggestions
        $(document).on('click', '.ai-title-suggestion', function() {
            const title = $(this).text();
            $('#post_title').val(title);
            $('#ai-title-suggestions').slideUp();
            showNotice('Title selected!', 'success');
        });
    }
    
    // AI Generate Title
    function handleAIGenerateTitle() {
        const content = getEditorContent();
        
        if (!content || content.trim() === '') {
            showNotice('Please write your blog content first, then AI can generate relevant titles', 'error');
            return;
        }
        
        $('#ai-title-loading').show();
        $('#ai-generate-title').prop('disabled', true);
        
        $.ajax({
            url: wpfrontblogger_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'wpfrontblogger_ai_generate_title',
                content: content,
                nonce: wpfrontblogger_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    displayTitleSuggestions(response.data.titles);
                } else {
                    const errorMessage = (response.data && response.data.message) ? response.data.message : 'Failed to generate titles';
                    showNotice(errorMessage, 'error');
                }
            },
            error: function() {
                showNotice('Error connecting to AI service', 'error');
            },
            complete: function() {
                $('#ai-title-loading').hide();
                $('#ai-generate-title').prop('disabled', false);
            }
        });
    }
    
    function displayTitleSuggestions(titles) {
        const suggestionsContainer = $('#ai-title-suggestions .ai-suggestion-list');
        suggestionsContainer.empty();
        
        if (titles && titles.length > 0) {
            titles.forEach(function(title) {
                const suggestion = $('<div class="ai-title-suggestion">' + title + '</div>');
                suggestionsContainer.append(suggestion);
            });
            
            $('#ai-title-suggestions').slideDown();
            showNotice('AI generated ' + titles.length + ' title suggestions. Click to select one.', 'success');
        } else {
            showNotice('AI could not generate titles for your content', 'warning');
        }
    }
    
    // AI Rewrite Content
    function handleAIRewriteContent() {
        const content = getEditorContent();
        
        if (!content || content.trim() === '') {
            showNotice('Please write your blog content first, then AI can rewrite it', 'error');
            return;
        }
        
        $('#ai-content-loading').show();
        $('#ai-rewrite-content').prop('disabled', true);
        
        $.ajax({
            url: wpfrontblogger_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'wpfrontblogger_ai_rewrite_content',
                content: content,
                nonce: wpfrontblogger_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    setEditorContent(response.data.content);
                    // Success notice removed - content is rewritten silently
                } else {
                    const errorMessage = (response.data && response.data.message) ? response.data.message : 'Failed to rewrite content';
                    showNotice(errorMessage, 'error');
                }
            },
            error: function() {
                showNotice('Error connecting to AI service', 'error');
            },
            complete: function() {
                $('#ai-content-loading').hide();
                $('#ai-rewrite-content').prop('disabled', false);
            }
        });
    }
    
    // AI Select Categories
    function handleAISelectCategories() {
        const content = getEditorContent();
        
        if (!content || content.trim() === '') {
            showNotice('Please write your blog content first, then AI can select relevant categories', 'error');
            return;
        }
        
        $('#ai-categories-loading').show();
        $('#ai-select-categories').prop('disabled', true);
        
        $.ajax({
            url: wpfrontblogger_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'wpfrontblogger_ai_select_categories',
                content: content,
                nonce: wpfrontblogger_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Clear existing categories
                    selectedCategories = [];
                    $('#selected-categories').empty();
                    
                    // Add AI-selected categories
                    response.data.categories.forEach(function(category) {
                        addSelectedItem('categories', category.id, category.label);
                    });
                    
                    showNotice('AI selected ' + response.data.categories.length + ' relevant categories!', 'success');
                } else {
                    const errorMessage = (response.data && response.data.message) ? response.data.message : 'Failed to select categories';
                    showNotice(errorMessage, 'error');
                }
            },
            error: function() {
                showNotice('Error connecting to AI service', 'error');
            },
            complete: function() {
                $('#ai-categories-loading').hide();
                $('#ai-select-categories').prop('disabled', false);
            }
        });
    }
    
    // AI Generate Tags
    function handleAIGenerateTags() {
        const content = getEditorContent();
        
        if (!content || content.trim() === '') {
            showNotice('Please write your blog content first, then AI can generate relevant tags', 'error');
            return;
        }
        
        $('#ai-tags-loading').show();
        $('#ai-generate-tags').prop('disabled', true);
        
        $.ajax({
            url: wpfrontblogger_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'wpfrontblogger_ai_generate_tags',
                content: content,
                nonce: wpfrontblogger_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Add AI-generated tags to existing ones
                    response.data.tags.forEach(function(tag) {
                        if (!isItemSelected('tags', tag.name)) {
                            addSelectedItem('tags', tag.id, tag.label, true);
                        }
                    });
                    
                    showNotice('AI generated ' + response.data.tags.length + ' relevant tags!', 'success');
                } else {
                    const errorMessage = (response.data && response.data.message) ? response.data.message : 'Failed to generate tags';
                    showNotice(errorMessage, 'error');
                }
            },
            error: function() {
                showNotice('Error connecting to AI service', 'error');
            },
            complete: function() {
                $('#ai-tags-loading').hide();
                $('#ai-generate-tags').prop('disabled', false);
            }
        });
    }
    
    // AI Select Products
    function handleAISelectProducts() {
        const content = getEditorContent();
        
        if (!content || content.trim() === '') {
            showNotice('Please write your blog content first, then AI can select relevant products', 'error');
            return;
        }
        
        $('#ai-products-loading').show();
        $('#ai-select-products').prop('disabled', true);
        
        $.ajax({
            url: wpfrontblogger_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'wpfrontblogger_ai_select_products',
                content: content,
                nonce: wpfrontblogger_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Add AI-selected products to existing ones
                    response.data.products.forEach(function(product) {
                        if (!isItemSelected('products', product.title)) {
                            addSelectedItem('products', product.id, product.title + ' (' + product.price + ')');
                        }
                    });
                    
                    showNotice('AI selected ' + response.data.products.length + ' relevant products!', 'success');
                } else {
                    const errorMessage = (response.data && response.data.message) ? response.data.message : 'Failed to select products';
                    showNotice(errorMessage, 'error');
                }
            },
            error: function() {
                showNotice('Error connecting to AI service', 'error');
            },
            complete: function() {
                $('#ai-products-loading').hide();
                $('#ai-select-products').prop('disabled', false);
            }
        });
    }
    
    // AI Find Image
    function handleAIFindImage() {
        const title = $('#post_title').val().trim();
        const content = getEditorContent();
        
        if (!title && (!content || content.trim() === '')) {
            showNotice('Please add a title or content first, then AI can find a relevant image', 'error');
            return;
        }
        
        $('#ai-image-loading').show();
        $('#ai-find-image').hide();
        
        $.ajax({
            url: wpfrontblogger_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'wpfrontblogger_ai_generate_image',
                title: title,
                content: content,
                nonce: wpfrontblogger_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    displayAIImageResult(response.data);
                } else {
                    const errorMessage = (response.data && response.data.message) ? response.data.message : 'Failed to find image';
                    showNotice(errorMessage, 'error');
                    $('#ai-find-image').show();
                }
            },
            error: function() {
                showNotice('Error connecting to AI service', 'error');
                $('#ai-find-image').show();
            },
            complete: function() {
                $('#ai-image-loading').hide();
            }
        });
    }
    
    function displayAIImageResult(data) {
        const resultsContainer = $('#ai-image-results .ai-image-suggestion');
        resultsContainer.empty();
        
        if (data.image) {
            const imageHtml = '<div class="envato-image" data-id="' + data.image.id + '" data-url="' + data.image.preview + '" data-title="' + data.image.title + '">' +
                '<img src="' + data.image.thumbnail + '" alt="' + data.image.title + '">' +
                '<div class="image-overlay">' +
                    '<h4>' + data.image.title + '</h4>' +
                    '<p>by ' + (data.image.author || 'Unknown') + '</p>' +
                '</div>' +
            '</div>';
            
            resultsContainer.html(imageHtml);
            
            // Show keywords used
            if (data.keywords && data.keywords.length > 0) {
                $('#ai-keywords-used').text(data.keywords.join(', '));
            }
            
            $('#ai-image-results, .ai-image-actions').show();
            
            if (data.message) {
                showNotice(data.message, 'success');
            }
        }
    }
    
    function handleUseAIImage() {
        const imageElement = $('#ai-image-results .envato-image');
        const imageId = imageElement.data('id');
        const imageUrl = imageElement.data('url');
        const imageTitle = imageElement.data('title');
        
        if (!imageId || !imageUrl) {
            showNotice('Image data is missing', 'error');
            return;
        }
        
        // Import the image
        importEnvatoImage(imageId, imageUrl, imageTitle, function(success) {
            if (success) {
                $('.ai-image-actions').hide();
                switchImageTab('upload'); // Switch back to upload tab to show selected image
            }
        });
    }
    
    // Helper function to set editor content
    function setEditorContent(content) {
        if (typeof tinymce !== 'undefined' && tinymce.get('post_content')) {
            tinymce.get('post_content').setContent(content);
        } else {
            $('#post_content').val(content);
        }
    }
    
    // Helper function to check if item is already selected
    function isItemSelected(type, value) {
        const selectedItems = type === 'categories' ? selectedCategories : 
                             type === 'tags' ? selectedTags : selectedProducts;
        
        return selectedItems.some(function(item) {
            return item.name === value || item.label === value;
        });
    }
});