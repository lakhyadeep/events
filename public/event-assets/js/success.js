// ==========================================================================
// VOTE SUCCESS PAGE LOGIC & TONI & GUY COUPON MANAGEMENT (v2)
// Dibrugarh Durgo Utsav 2026
// ==========================================================================

const pujasDatabase = (window.dynamicPujasData && Array.isArray(window.dynamicPujasData))
    ? window.dynamicPujasData
    : [];

$(document).ready(function () {
    loadVoteConfirmationDetails();
    setupCouponCopy();
    setupWhatsAppShare();
});

function loadVoteConfirmationDetails() {
    // 1. Check server data bridge from Blade
    let pandal = window.serverVotedPandal || null;
    let category = window.serverVotedCategory || null;
    let mobile = window.serverVotedMobile || null;

    // 2. Check URL path slug: /vote-success/{participant}
    const pathParts = window.location.pathname.replace(/\/+$/, '').split('/').filter(Boolean);
    const successIdx = pathParts.indexOf('vote-success');
    if (successIdx !== -1 && pathParts.length > successIdx + 1) {
        const rawSlug = decodeURIComponent(pathParts[successIdx + 1]);
        const matchedBySlug = pujasDatabase.find(p => (p.slug && p.slug === rawSlug) || String(p.id) === rawSlug);
        if (matchedBySlug) {
            pandal = pandal || matchedBySlug.name;
        }
    }

    // 3. Check legacy URL search parameters if any remain
    const urlParams = new URLSearchParams(window.location.search);
    pandal = pandal || urlParams.get('pandal');
    category = category || urlParams.get('category') || urlParams.get('feature');
    mobile = mobile || urlParams.get('mobile');

    // 4. Fallbacks from localStorage if not in URL or server
    if (!pandal) {
        try {
            pandal = localStorage.getItem('last_voted_pandal') || localStorage.getItem('selected_pandal');
        } catch (e) {}
    }

    if (!category) {
        try {
            category = localStorage.getItem('last_voted_category');
        } catch (e) {}
    }

    if (!mobile) {
        try {
            mobile = localStorage.getItem('last_voted_mobile');
        } catch (e) {}
    }

    pandal = pandal || "";
    category = category || "";
    mobile = mobile || "";

    // Lookup metadata
    const cleanName = pandal ? decodeURIComponent(pandal).trim() : "";
    const matched = cleanName ? (pujasDatabase.find(p => p.name.toLowerCase() === cleanName.toLowerCase()) ||
                    pujasDatabase.find(p => p.name.toLowerCase().includes(cleanName.toLowerCase()))) : null;

    const targetName = matched ? matched.name : cleanName;
    const targetLoc = matched ? (matched.location || "") : "";

    // Mask phone number: +91 ••••• •••99
    let maskedMobile = mobile ? ('+91 ••••• •••' + (mobile.length >= 2 ? mobile.slice(-2) : '')) : '';

    // Category Label format
    let categoryLabel = category ? decodeURIComponent(category) : '';
    if (['Theme', 'Pandal', 'Idol'].includes(categoryLabel)) {
        categoryLabel = 'Best ' + categoryLabel;
    }

    let voterName = null;
    try {
        voterName = localStorage.getItem('last_voted_name');
    } catch (e) {}

    // Populate UI
    $('#confirmedPandalName').text(targetName);

    if (voterName && voterName.trim()) {
        $('#confirmedVoterName').text(voterName.trim());
        $('#confirmedVoterNameWrap').show();
    } else {
        $('#confirmedVoterNameWrap').hide();
    }

    if (categoryLabel) {
        $('#confirmedCategoryBadge').text(categoryLabel).show();
    } else {
        $('#confirmedCategoryBadge').hide();
    }

    if (targetLoc) {
        $('#confirmedPandalLoc').text(targetLoc);
        $('#confirmedPandalLocWrap').show();
    } else {
        $('#confirmedPandalLocWrap').hide();
    }

    if (maskedMobile) {
        $('#confirmedVoterMobile').text(maskedMobile);
        $('#confirmedVoterMobileWrap').show();
    } else {
        $('#confirmedVoterMobileWrap').hide();
    }
}

function setupCouponCopy() {
    $('#btnCopyCoupon').on('click', function () {
        const couponCode = $('#couponCodeDisplay').text().trim();

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(couponCode).then(showCopySuccess).catch(fallbackCopy);
        } else {
            fallbackCopy();
        }

        function fallbackCopy() {
            const $temp = $('<input>');
            $('body').append($temp);
            $temp.val(couponCode).select();
            document.execCommand('copy');
            $temp.remove();
            showCopySuccess();
        }

        function showCopySuccess() {
            $('#copySuccessAlert').removeClass('d-none').hide().fadeIn(200);
            $('#copyBtnText').text('Copied!');
            $('#copyIcon').removeClass('bi-clipboard').addClass('bi-clipboard-check-fill');
            $('#btnCopyCoupon').css('background', 'linear-gradient(135deg, #10b981 0%, #059669 100%)').css('color', '#ffffff');

            setTimeout(function () {
                $('#copySuccessAlert').fadeOut(300);
                $('#copyBtnText').text('Copy Code');
                $('#copyIcon').removeClass('bi-clipboard-check-fill').addClass('bi-clipboard');
                $('#btnCopyCoupon').css('background', '').css('color', '');
            }, 3000);
        }
    });
}

function setupWhatsAppShare() {
    function getWhatsAppShareUrl() {
        const pandal = $('#confirmedPandalName').text().trim() || window.serverVotedPandal || 'my favorite Durga Puja';
        const eventTitle = window.serverEventName || 'Dibrugarh Sharad Samman 2026';
        let allPujasLink = window.allPujasUrl || $('#btnShareWhatsApp').data('all-pujas-url') || (window.location.origin + '/all-pujas');

        if (window.location && window.location.origin) {
            try {
                if (window.location.hostname !== 'events.test' || window.location.protocol === 'https:') {
                    allPujasLink = window.location.origin + '/all-pujas';
                }
            } catch (e) {}
        }

        const shareText = `🎉 I just voted for *${pandal}* in ${eventTitle}! 🏆\n\nCast your vote for your favorite puja and claim an exclusive *FLAT 20% OFF* Salon Voucher from *TONI&GUY Dibrugarh*!\n\n👉 Vote for all pujas here:\n${allPujasLink}`;

        return 'https://wa.me/?text=' + encodeURIComponent(shareText);
    }

    function updateWhatsAppShareLink() {
        const whatsappUrl = getWhatsAppShareUrl();
        $('#btnShareWhatsApp').attr('href', whatsappUrl);
    }

    updateWhatsAppShareLink();

    $('#btnShareWhatsApp').on('click', function () {
        updateWhatsAppShareLink();
    });
}

