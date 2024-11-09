document.addEventListener('DOMContentLoaded', function () {
  const ELEMENTS = {
    form: document.querySelector('.wp-feedback-form'),
    positiveFeedbackButton: document.querySelector('.positive'),
    negativeFeedbackButton: document.querySelector('.negative'),
    feedbackCommentWrapper: document.querySelector('.feedback-comment-wrap'),
    feedbackComment: document.querySelector('.feedback-comment'),
    feedbackSubmitButton: document.querySelector('.feedback-submit'),
    feedbackCancelButton: document.querySelector('.feedback-cancel'),
    feedbackMessage: document.querySelector('.feedback-message'),
  };

  const postId = ELEMENTS.form?.dataset.postId;
  let feedbackType = '';
  let isSubmitting = false;

  if (ELEMENTS.form) {
    ELEMENTS.positiveFeedbackButton?.addEventListener('click', () => {
      ELEMENTS.positiveFeedbackButton.classList.add('selected');
      feedbackType = ELEMENTS.positiveFeedbackButton.dataset.type;
      showCommentForm();
    });

    // Negative feedback button click
    ELEMENTS.negativeFeedbackButton?.addEventListener('click', () => {
      ELEMENTS.negativeFeedbackButton.classList.add('selected');
      feedbackType = ELEMENTS.negativeFeedbackButton.dataset.type;
      showCommentForm();
    });

    // Cancel button click
    ELEMENTS.feedbackCancelButton?.addEventListener('click', () => {
      hideCommentForm();

      clearMessage();
    });

    // Submit button click
    ELEMENTS.feedbackSubmitButton?.addEventListener('click', () => {
      if (isSubmitting) return;

      const comment = ELEMENTS.feedbackComment?.value || '';
      const data = new FormData();

      data.append('action', 'submit_feedback');
      data.append('post_id', postId);
      data.append('feedback_type', feedbackType);
      data.append('comment', comment.trim());
      data.append('nonce', wpFeedbackForm.nonce);

      handleSubmitFeedback(data);
    });
  }

  /**
   * Handle feedback submission
   * @param {FormData} data
   */
  const handleSubmitFeedback = async (data) => {
    try {
      setLoadingState(true);

      clearMessage();

      const response = await fetch(wpFeedbackForm.ajaxurl, {
        method: 'POST',
        credentials: 'same-origin',
        body: data,
      });

      const result = await response.json();

      if (result.success) {
        showMessage(result.data.message, 'success');
        resetForm();
      } else {
        throw new Error(result.data.message || 'Submission failed');
      }
    } catch (error) {
      console.error('Feedback submission error:', error);
      showMessage(wpFeedbackForm.messages.error, 'error');
    } finally {
      setLoadingState(false);
    }
  };

  /**
   * Show the comment form
   */
  const showCommentForm = () => {
    if (ELEMENTS.feedbackCommentWrapper) {
      ELEMENTS.feedbackCommentWrapper.style.display = 'block';
    }
  };

  /**
   * Hide the comment form
   */
  const hideCommentForm = () => {
    if (ELEMENTS.feedbackCommentWrapper) {
      ELEMENTS.feedbackCommentWrapper.style.display = 'none';
    }
  };

  /**
   * Reset the entire form
   */
  const resetForm = () => {
    hideCommentForm();
    if (ELEMENTS.feedbackComment) {
      ELEMENTS.feedbackComment.value = '';
    }
  };

  /**
   * Set loading state
   * @param {boolean} isLoading
   */
  const setLoadingState = (isLoading) => {
    isSubmitting = isLoading;

    if (ELEMENTS.feedbackSubmitButton) {
      ELEMENTS.feedbackSubmitButton.disabled = isLoading;
      ELEMENTS.feedbackSubmitButton.innerHTML = isLoading
        ? '<span class="loading-spinner"></span> ' +
          wpFeedbackForm.messages.submitting
        : wpFeedbackForm.messages.submit;
    }

    if (ELEMENTS.positiveFeedbackButton) {
      ELEMENTS.positiveFeedbackButton.disabled = isLoading;
    }
    if (ELEMENTS.negativeFeedbackButton) {
      ELEMENTS.negativeFeedbackButton.disabled = isLoading;
    }
  };

  /**
   * Show message
   * @param {string} text
   * @param {string} type
   */
  const showMessage = (text, type) => {
    if (ELEMENTS.feedbackMessage) {
      ELEMENTS.feedbackMessage.className = `feedback-message ${type}`;
      ELEMENTS.feedbackMessage.textContent = text;
      ELEMENTS.feedbackMessage.style.display = 'block';

      if (type === 'success') {
        setTimeout(clearMessage, 3000);
      }
    }
  };

  /**
   * Clear message
   */
  const clearMessage = () => {
    if (ELEMENTS.feedbackMessage) {
      ELEMENTS.feedbackMessage.style.display = 'none';
      ELEMENTS.feedbackMessage.textContent = '';
    }
  };
});
