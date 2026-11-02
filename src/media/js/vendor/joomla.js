// File#: _1_joomla

(function () {
	/**
	 * Removes the main article section when the article body has no content.
	 *
	 * @returns {void}
	 */
	function removeEmptyArticleSection() {
		const body = document.querySelector('.com-content-article__body');

		if (!body || body.textContent.trim() !== '') {
			return;
		}

		const article = body.closest('.com-content-article');
		const entryContent = article ? article.closest('.entry-content') : null;
		const section = entryContent ? entryContent.parentElement : article;

		if (section) {
			section.remove();
		}
	}

	/**
	 * Returns whether a style element is the Convert Forms color palette.
	 *
	 * @param {HTMLStyleElement} style - Style element to inspect
	 * @returns {boolean} True when the element sets Convert Forms color variables
	 */
	function isConvertFormsColorStyle(style) {
		const css = style.textContent || '';

		return css.includes('.convertforms') && css.includes('--color-primary');
	}

	/**
	 * Removes a Convert Forms color style element when it matches.
	 *
	 * @param {HTMLStyleElement} style - Style element to remove
	 * @returns {void}
	 */
	function removeConvertFormsColorStyle(style) {
		if (style.tagName === 'STYLE' && isConvertFormsColorStyle(style)) {
			style.remove();
		}
	}

	/**
	 * Removes Convert Forms inline color variables already present in the document.
	 *
	 * @param {ParentNode} [root=document] - Node to search within
	 * @returns {void}
	 */
	function removeConvertFormsColorStyles(root = document) {
		root.querySelectorAll('style').forEach(removeConvertFormsColorStyle);
	}

	/**
	 * Removes a node when it is, or contains, a Convert Forms color style.
	 *
	 * @param {Node} node - Node added to the document
	 * @returns {void}
	 */
	function removeAddedConvertFormsColorStyle(node) {
		if (node.nodeType === Node.ELEMENT_NODE) {
			const element = /** @type {Element} */ (node);

			if (element.tagName === 'STYLE') {
				removeConvertFormsColorStyle(/** @type {HTMLStyleElement} */(element));
				return;
			}

			removeConvertFormsColorStyles(element);
			return;
		}

		if (node.nodeType === Node.TEXT_NODE && node.parentElement?.tagName === 'STYLE') {
			removeConvertFormsColorStyle(/** @type {HTMLStyleElement} */(node.parentElement));
		}
	}

	/**
	 * Watches for Convert Forms color styles injected after the initial parse.
	 *
	 * @returns {void}
	 */
	function watchConvertFormsColorStyles() {
		removeConvertFormsColorStyles();

		const observer = new MutationObserver((mutations) => {
			mutations.forEach((mutation) => {
				mutation.addedNodes.forEach(removeAddedConvertFormsColorStyle);
			});
		});

		observer.observe(document.documentElement, {
			childList: true,
			subtree: true,
		});
	}

	removeEmptyArticleSection();
	watchConvertFormsColorStyles();
})();
