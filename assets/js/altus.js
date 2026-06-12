jQuery(function ($) {

    /* ── colour bands ─────────────────────────────────────── */
    function scoreColour(n) {
        if (n === null || n === undefined) return '#aaa';
        if (n >= 90) return '#0cce6b';   // green
        if (n >= 50) return '#ffa400';   // amber
        return '#ff4e42';                // red
    }

    function scoreLabel(n) {
        if (n === null || n === undefined) return 'N/A';
        return n + ' / 100';
    }

    /* ── one score card ───────────────────────────────────── */
    function card(label, value) {
        var colour = scoreColour(value);
        var display = (value === null || value === undefined) ? 'N/A' : value;
        return (
            '<div style="flex:1;min-width:120px;padding:16px 10px;border-radius:8px;' +
            'border:2px solid ' + colour + ';text-align:center;">' +
            '<div style="font-size:32px;font-weight:700;color:' + colour + ';">' + display + '</div>' +
            '<div style="font-size:12px;color:#555;margin-top:6px;">' + label + '</div>' +
            '</div>'
        );
    }

    /* ── render a device section ──────────────────────────── */
    function deviceBlock(label, scores) {
        return (
            '<div style="margin-bottom:20px;">' +
            '<h3 style="margin:0 0 12px;font-size:15px;color:#333;text-transform:uppercase;' +
            'letter-spacing:.05em;">' + label + '</h3>' +
            '<div style="display:flex;gap:12px;flex-wrap:wrap;">' +
            card('Performance',    scores.performance) +
            card('SEO',            scores.seo) +
            card('Accessibility',  scores.accessibility) +
            card('Best Practices', scores.best_practices) +
            '</div>' +
            '</div>'
        );
    }

    /* ── button click ─────────────────────────────────────── */
    $(document).on('click', '#altus-run', function () {
        var url = $.trim($('#altus-url').val());
        var $result = $('#altus-result');

        if (!url) {
            $result.html('<p style="color:#c00;">Please enter a URL.</p>');
            return;
        }

        // basic protocol check
        if (!/^https?:\/\//i.test(url)) {
            url = 'https://' + url;
            $('#altus-url').val(url);
        }

        $result.html(
            '<p style="color:#555;">⏳ Running audit — this can take 15–30 seconds…</p>'
        );

        $.post(
            altus_ajax.ajax_url,
            {
                action: 'altus_run_audit',
                nonce:  altus_ajax.nonce,
                url:    url
            },
            function (res) {
                if (!res.success) {
                    $result.html(
                        '<p style="color:#c00;">❌ ' + (res.data || 'Audit failed.') + '</p>'
                    );
                    return;
                }

                var d = res.data;
                var html =
                    '<div style="border:1px solid #e0e0e0;border-radius:8px;padding:20px;">' +
                    '<p style="margin:0 0 16px;font-size:13px;color:#666;">Results for: <strong>' +
                    $('<span>').text(d.url).html() + '</strong></p>' +
                    deviceBlock('📱 Mobile',  d.mobile) +
                    deviceBlock('🖥 Desktop', d.desktop) +
                    '<p style="font-size:11px;color:#999;margin:12px 0 0;">Powered by Google PageSpeed Insights</p>' +
                    '</div>';

                $result.html(html);
            }
        ).fail(function () {
            $result.html('<p style="color:#c00;">❌ Request failed. Please try again.</p>');
        });
    });

    /* ── allow Enter key in input ─────────────────────────── */
    $(document).on('keypress', '#altus-url', function (e) {
        if (e.which === 13) $('#altus-run').trigger('click');
    });
});