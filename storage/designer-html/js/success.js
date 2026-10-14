// ==========================================================================
// VOTE SUCCESS PAGE LOGIC & TONI & GUY COUPON MANAGEMENT
// Dibrugarh Durgo Utsav 2026
// ==========================================================================

const pujasDatabase = [
    { id: 1, name: "Thana Chariali Sarbajanin", location: "Thana Chariali, Central Dibrugarh", theme: "Royal Vedic Mandir with Intricate Brass Relief" },
    { id: 2, name: "Graham Bazar Sarbajanin", location: "Graham Bazar, Dibrugarh", theme: "Artisanal Terracotta & Clay Heritage Architecture" },
    { id: 3, name: "New Market Durga Puja", location: "New Market Road, Dibrugarh", theme: "Golden Palace Recreated with Traditional Assamese Motifs" },
    { id: 4, name: "Jalan Nagar Sarbajanin Pratima", location: "Jalan Nagar, Dibrugarh", theme: "Sculpted Sabeki Ekchala Durga Idol in Pristine White Sholar Saaj" },
    { id: 5, name: "Kadmani Sarbajanin Pratima", location: "Kadmani, Dibrugarh", theme: "Lifelike Clay Eyes & Mahishasurmardini Dynamic Stance" },
    { id: 6, name: "Graham Bazar Cultural Pratima", location: "Graham Bazar, Dibrugarh", theme: "Natural Eco-friendly Clay Colors with Classical Draping" },
    { id: 7, name: "Marwari Patty Sarbajanin", location: "Marwari Patty, Dibrugarh", theme: "3D Dynamic LED Chandannagar Moving Light Gates" },
    { id: 8, name: "Railway Colony Puja Samiti", location: "Railway Colony, Dibrugarh", theme: "Spectacular Synchronized Light Symphony & Canopy" },
    { id: 9, name: "Shantipara Sarbajanin Durga Puja", location: "Shantipara 2nd Lane, Dibrugarh", theme: "Swarna Mandir Golden Splendor with Dazzling Illumination" }
];

$(document).ready(function () {
    loadVoteConfirmationDetails();
    setupCouponCopy();
    setupWhatsAppShare();
});

function loadVoteConfirmationDetails() {
    const urlParams = new URLSearchParams(window.location.search);
    let pandal = urlParams.get('pandal');
    let category = urlParams.get('category') || urlParams.get('feature');
    let mobile = urlParams.get('mobile');

    // Fallbacks from localStorage if not in URL
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

    // Default fallbacks
    pandal = pandal || "Graham Bazar Sarbajanin";
    category = category || "Overall(Best of the best)";
    mobile = mobile || "9876543299";

    // Lookup metadata
    const cleanName = decodeURIComponent(pandal).trim();
    const matched = pujasDatabase.find(p => p.name.toLowerCase() === cleanName.toLowerCase()) ||
                    pujasDatabase.find(p => p.name.toLowerCase().includes(cleanName.toLowerCase()));

    const targetName = matched ? matched.name : cleanName;
    const targetLoc = matched ? matched.location : "Dibrugarh, Assam";

    // Mask phone number: +91 98•••• ••99
    let maskedMobile = '+91 98•••• ••' + (mobile.length >= 2 ? mobile.slice(-2) : '99');

    // Populate UI
    $('#confirmedPandalName').text(targetName);
    $('#confirmedCategoryBadge').text(decodeURIComponent(category));
    $('#confirmedPandalLoc').text(targetLoc);
    $('#confirmedVoterMobile').text(maskedMobile);
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
    const pandal = $('#confirmedPandalName').text() || 'my favorite Durga Puja';
    const currentBaseUrl = window.location.href.split('vote-success.html')[0] || '';
    const allPujasLink = currentBaseUrl + 'allpujas.html';

    const shareText = `🎉 I just voted for *${pandal}* in Dibrugarh Sharad Samman 2026! 🏆\n\nCast your vote for your favorite puja and claim an exclusive *FLAT 20% OFF* Salon Voucher from *TONI&GUY Dibrugarh*!\n\n👉 Vote for all pujas here:\n${allPujasLink}`;

    const whatsappUrl = 'https://wa.me/?text=' + encodeURIComponent(shareText);
    $('#btnShareWhatsApp').attr('href', whatsappUrl);
}
