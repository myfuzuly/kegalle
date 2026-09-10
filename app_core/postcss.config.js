import purgecss from '@fullhuman/postcss-purgecss'

const isProd = process.env.NODE_ENV === 'production'

export default {
    plugins: [
        isProd && purgecss({
            content: [
                './resources/views/**/*.blade.php',
                './resources/js/**/*.{js,vue}',
                './public/js/**/*.js',
            ],
            defaultExtractor: content => {
                // Grab all Blade class strings, including conditional ones
                const broadMatches = content.match(/[^<>"'`\s]*[^<>"'`\s:]/g) || []
                const innerMatches = content.match(/[^<>"'`\s.()]*[^<>"'`\s.():]/g) || []
                return broadMatches.concat(innerMatches)
            },
            safelist: {
                // Keep ALL site-wide k-* class families + dynamic/JS-toggled classes
                standard: [
                    // All custom k-* prefixes used site-wide
                    /^k-/,
                    /^kfl-/,
                    /^kpc/,
                    /^kg-/,
                    /^kd-/,
                    /^kdl-/,
                    /^ksp-/,
                    /^kf-/,
                    /^km-/,
                    /^kt-/,
                    /^ksd/,
                    /^dbi-/,
                    /^cf-/,
                    /^gs-/,
                    /^gss-/,
                    /^bl-/,
                    /^select2/,
                    // State/toggle classes
                    /^dark$/,
                    /^open$/,
                    /^active$/,
                    /^show$/,
                    /^hidden$/,
                    /^visible$/,
                    /^collapsed$/,
                    /^is-/,
                    /^has-/,
                    /dark$/,
                    /^cnt$/,
                    /^kex/,
                    /^khfs/,
                    /^khi/,
                ],
                deep: [/^(is|has|data|aria)-/],
                greedy: [/^select2/, /^ksd/, /^dbi/, /^kfl/, /^kg-/, /^kpc/, /^k-/, /^kex/, /^khfs/, /^khi/, /^kex/],
            },
        }),
    ].filter(Boolean),
}
