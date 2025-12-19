<?php
/**
 * Plugin Name: WXP Salgsfordele
 * Description: Konfigurerbar banner med salgsfordele - separate indstillinger for desktop og mobil
 * Version: 2.0.4
 * Author: Prokop Suchanek // wxp.dk
 * GitHub Plugin URI: wxp-dk/wxp-salgsfordele
 * Primary Branch: main
 */

/**
 * WXP Salgsfordele with Admin Settings under WooCommerce
 * Version 2.0.3 features:
 * - Optional URL link
 * - Separate settings for General, Desktop, and Mobile
 * - Separate colors for desktop and mobile
 * - Font size control for desktop and mobile
 * - Generic WordPress hooks for broad theme compatibility
 * - Mobile: scroll or slide animation
 * - Separate padding height for desktop and mobile
 * - Separate speed/distance settings for desktop and mobile
 */

// ---------- Defaults ----------
function wxp_salgsfordele_default_options() {
    return array(
        // General settings
        'vis'      => 'JA',
        'url'      => '',
        'url_enabled' => 'NEJ',
        
        // Desktop settings
        'desktop_bgfarve'  => '#f8d8cc',
        'desktop_txtfarve' => '#000000',
        'desktop_font'     => 'Arial, sans-serif',
        'desktop_fontsize' => 16,
        'desktop_ticker'   => 'JA',
        'desktop_speed'    => 20,
        'desktop_padding'  => 40,
        'desktop_height'   => 12,
        
        // Mobile settings
        'mobile_bgfarve'   => '#f8d8cc',
        'mobile_txtfarve'  => '#000000',
        'mobile_font'      => 'Arial, sans-serif',
        'mobile_fontsize'  => 14,
        'mobile_animation' => 'slide',
        'mobile_speed'     => 20,
        'mobile_interval'  => 5,
        'mobile_padding'   => 40,
        'mobile_height'    => 12,
        
        // Text fields
        'text_1'   => '628 tilbud du IKKE vil gå glip af! 🔥',
        'text_2'   => '',
        'text_3'   => '',
        'text_4'   => '',
        'text_5'   => '',
        'text_6'   => '',
        'text_7'   => '',
        'text_8'   => '',
    );
}

// ---------- Get merged options ----------
function wxp_salgsfordele_get_options() {
    $defaults = wxp_salgsfordele_default_options();
    $saved    = get_option('wxp_salgsfordele_options', array());
    if (!is_array($saved)) $saved = array();
    return array_merge($defaults, $saved);
}

// ---------- Sanitize ----------
function wxp_salgsfordele_sanitize_options($input) {
    $out = array();
    
    // vis: keep "JA"/"NEJ"
    $out['vis'] = (!empty($input['vis']) && $input['vis'] === 'JA') ? 'JA' : 'NEJ';
    
    // url_enabled: keep "JA"/"NEJ"
    $out['url_enabled'] = (!empty($input['url_enabled']) && $input['url_enabled'] === 'JA') ? 'JA' : 'NEJ';
    
    // url: sanitize_url
    $url = isset($input['url']) ? trim($input['url']) : '';
    $out['url'] = esc_url_raw($url);
    
    // Desktop: ticker toggle
    $out['desktop_ticker'] = (!empty($input['desktop_ticker']) && $input['desktop_ticker'] === 'JA') ? 'JA' : 'NEJ';
    
    // Desktop: speed (5-60 seconds)
    $desktop_speed = isset($input['desktop_speed']) ? intval($input['desktop_speed']) : 20;
    $out['desktop_speed'] = max(5, min(60, $desktop_speed));
    
    // Desktop: padding (0-200 pixels)
    $desktop_padding = isset($input['desktop_padding']) ? intval($input['desktop_padding']) : 40;
    $out['desktop_padding'] = max(0, min(200, $desktop_padding));
    
    // Desktop: height (0-50 pixels)
    $desktop_height = isset($input['desktop_height']) ? intval($input['desktop_height']) : 12;
    $out['desktop_height'] = max(0, min(50, $desktop_height));
    
    // Desktop: font size (10-32 pixels)
    $desktop_fontsize = isset($input['desktop_fontsize']) ? intval($input['desktop_fontsize']) : 16;
    $out['desktop_fontsize'] = max(10, min(32, $desktop_fontsize));
    
    // Desktop: colors
    foreach (array('desktop_bgfarve', 'desktop_txtfarve') as $k) {
        $val = isset($input[$k]) ? trim($input[$k]) : '';
        if (preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $val)) {
            $out[$k] = $val;
        } else {
            $out[$k] = wxp_salgsfordele_default_options()[$k];
        }
    }
    
    // Desktop: font
    $out['desktop_font'] = isset($input['desktop_font']) ? sanitize_text_field($input['desktop_font']) : wxp_salgsfordele_default_options()['desktop_font'];
    
    // Mobile: animation type
    $mobile_animation = isset($input['mobile_animation']) ? $input['mobile_animation'] : 'slide';
    $out['mobile_animation'] = in_array($mobile_animation, array('slide', 'scroll')) ? $mobile_animation : 'slide';
    
    // Mobile: speed (5-60 seconds) for scroll animation
    $mobile_speed = isset($input['mobile_speed']) ? intval($input['mobile_speed']) : 20;
    $out['mobile_speed'] = max(5, min(60, $mobile_speed));
    
    // Mobile: interval (2-20 seconds) for slide animation
    $mobile_interval = isset($input['mobile_interval']) ? intval($input['mobile_interval']) : 5;
    $out['mobile_interval'] = max(2, min(20, $mobile_interval));
    
    // Mobile: padding (0-200 pixels)
    $mobile_padding = isset($input['mobile_padding']) ? intval($input['mobile_padding']) : 40;
    $out['mobile_padding'] = max(0, min(200, $mobile_padding));
    
    // Mobile: height (0-50 pixels)
    $mobile_height = isset($input['mobile_height']) ? intval($input['mobile_height']) : 12;
    $out['mobile_height'] = max(0, min(50, $mobile_height));
    
    // Mobile: font size (10-32 pixels)
    $mobile_fontsize = isset($input['mobile_fontsize']) ? intval($input['mobile_fontsize']) : 14;
    $out['mobile_fontsize'] = max(10, min(32, $mobile_fontsize));
    
    // Mobile: colors
    foreach (array('mobile_bgfarve', 'mobile_txtfarve') as $k) {
        $val = isset($input[$k]) ? trim($input[$k]) : '';
        if (preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $val)) {
            $out[$k] = $val;
        } else {
            $out[$k] = wxp_salgsfordele_default_options()[$k];
        }
    }
    
    // Mobile: font
    $out['mobile_font'] = isset($input['mobile_font']) ? sanitize_text_field($input['mobile_font']) : wxp_salgsfordele_default_options()['mobile_font'];
    
    // 8 text fields: allow plain text, strip tags
    for ($i = 1; $i <= 8; $i++) {
        $key = 'text_' . $i;
        $out[$key] = isset($input[$key]) ? wp_strip_all_tags($input[$key]) : '';
    }
    
    return $out;
}

// ---------- Admin: menu ----------
add_action('admin_menu', function () {
    add_submenu_page(
        'woocommerce',
        __('Salgsfordele', 'wxp'),
        __('Salgsfordele', 'wxp'),
        'manage_woocommerce',
        'wxp-salgsfordele',
        'wxp_salgsfordele_settings_page_render',
        60
    );
});

// ---------- Admin: settings registration ----------
add_action('admin_init', function () {
    register_setting('wxp_salgsfordele_group', 'wxp_salgsfordele_options', 'wxp_salgsfordele_sanitize_options');
    
    // General settings section
    add_settings_section(
        'wxp_salgsfordele_general',
        __('Generelle indstillinger', 'wxp'),
        function () {
            echo '<p>' . esc_html__('Aktivér banneret og vælg tekster.', 'wxp') . '</p>';
        },
        'wxp_salgsfordele_page_general'
    );
    
    // Desktop settings section
    add_settings_section(
        'wxp_salgsfordele_desktop',
        __('Desktop indstillinger', 'wxp'),
        function () {
            echo '<p>' . esc_html__('Farver, animation og udseende for desktop visning.', 'wxp') . '</p>';
        },
        'wxp_salgsfordele_page_desktop'
    );
    
    // Mobile settings section
    add_settings_section(
        'wxp_salgsfordele_mobile',
        __('Mobil indstillinger', 'wxp'),
        function () {
            echo '<p>' . esc_html__('Farver, animation og udseende for mobil visning.', 'wxp') . '</p>';
        },
        'wxp_salgsfordele_page_mobile'
    );
    
    // === GENERAL SETTINGS ===
    
    // Vis (switch)
    add_settings_field('wxp_vis', __('Aktivér banner', 'wxp'), function () {
        $opts = wxp_salgsfordele_get_options();
        ?>
        <label>
            <input type="checkbox" name="wxp_salgsfordele_options[vis]" value="JA" <?php checked($opts['vis'], 'JA'); ?> />
            <?php esc_html_e('Vis banner på hjemmesiden', 'wxp'); ?>
        </label>
        <?php
    }, 'wxp_salgsfordele_page_general', 'wxp_salgsfordele_general');
    
    // URL enabled toggle
    add_settings_field('wxp_url_enabled', __('Link', 'wxp'), function () {
        $opts = wxp_salgsfordele_get_options();
        ?>
        <label>
            <input type="checkbox" name="wxp_salgsfordele_options[url_enabled]" value="JA" <?php checked($opts['url_enabled'], 'JA'); ?> id="wxp_url_toggle" />
            <?php esc_html_e('Gør banner klikbart', 'wxp'); ?>
        </label>
        <?php
    }, 'wxp_salgsfordele_page_general', 'wxp_salgsfordele_general');
    
    // URL field
    add_settings_field('wxp_url', __('Link URL', 'wxp'), function () {
        $opts = wxp_salgsfordele_get_options();
        ?>
        <input type="url" name="wxp_salgsfordele_options[url]" value="<?php echo esc_attr($opts['url']); ?>" 
               class="regular-text" id="wxp_url_field" 
               placeholder="https://example.com/tilbud/" />
        <p class="description"><?php esc_html_e('URL som brugeren sendes til ved klik på banneret', 'wxp'); ?></p>
        <script>
        (function() {
            var toggle = document.getElementById('wxp_url_toggle');
            var field = document.getElementById('wxp_url_field');
            function updateField() {
                field.disabled = !toggle.checked;
                field.style.opacity = toggle.checked ? '1' : '0.5';
            }
            toggle.addEventListener('change', updateField);
            updateField();
        })();
        </script>
        <?php
    }, 'wxp_salgsfordele_page_general', 'wxp_salgsfordele_general');
    
    // 8 text fields
    for ($i = 1; $i <= 8; $i++) {
        add_settings_field('wxp_text_' . $i, sprintf(__('Tekst %d', 'wxp'), $i), function () use ($i) {
            $opts = wxp_salgsfordele_get_options();
            $key = 'text_' . $i;
            ?>
            <input type="text" name="wxp_salgsfordele_options[<?php echo esc_attr($key); ?>]" 
                   value="<?php echo esc_attr($opts[$key]); ?>" class="regular-text" 
                   placeholder="<?php echo esc_attr(sprintf(__('Indtast tekst %d...', 'wxp'), $i)); ?>" />
            <?php
        }, 'wxp_salgsfordele_page_general', 'wxp_salgsfordele_general');
    }
    
    // === DESKTOP SETTINGS ===
    
    // Desktop background color
    add_settings_field('wxp_desktop_bgfarve', __('Baggrundsfarve', 'wxp'), function () {
        $opts = wxp_salgsfordele_get_options();
        ?>
        <input type="color" name="wxp_salgsfordele_options[desktop_bgfarve]" value="<?php echo esc_attr($opts['desktop_bgfarve']); ?>" />
        <?php
    }, 'wxp_salgsfordele_page_desktop', 'wxp_salgsfordele_desktop');
    
    // Desktop text color
    add_settings_field('wxp_desktop_txtfarve', __('Tekstfarve', 'wxp'), function () {
        $opts = wxp_salgsfordele_get_options();
        ?>
        <input type="color" name="wxp_salgsfordele_options[desktop_txtfarve]" value="<?php echo esc_attr($opts['desktop_txtfarve']); ?>" />
        <?php
    }, 'wxp_salgsfordele_page_desktop', 'wxp_salgsfordele_desktop');
    
    // Desktop font
    add_settings_field('wxp_desktop_font', __('Skrifttype', 'wxp'), function () {
        $opts = wxp_salgsfordele_get_options();
        $fonts = array(
            'Arial, sans-serif' => 'Arial',
            'Helvetica, sans-serif' => 'Helvetica',
            'Georgia, serif' => 'Georgia',
            'Times New Roman, serif' => 'Times New Roman',
            'Courier New, monospace' => 'Courier New',
            'Verdana, sans-serif' => 'Verdana',
            'Trebuchet MS, sans-serif' => 'Trebuchet MS',
            'Impact, sans-serif' => 'Impact',
            'Comic Sans MS, cursive' => 'Comic Sans MS',
        );
        ?>
        <select name="wxp_salgsfordele_options[desktop_font]">
            <?php foreach ($fonts as $value => $label) : ?>
                <option value="<?php echo esc_attr($value); ?>" <?php selected($opts['desktop_font'], $value); ?>>
                    <?php echo esc_html($label); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php
    }, 'wxp_salgsfordele_page_desktop', 'wxp_salgsfordele_desktop');
    
    // Desktop font size
    add_settings_field('wxp_desktop_fontsize', __('Skriftstørrelse', 'wxp'), function () {
        $opts = wxp_salgsfordele_get_options();
        ?>
        <input type="number" name="wxp_salgsfordele_options[desktop_fontsize]" 
               min="10" max="32" step="1" 
               value="<?php echo esc_attr($opts['desktop_fontsize']); ?>" 
               class="small-text" /> px
        <p class="description"><?php esc_html_e('Tekststørrelse (10-32 px)', 'wxp'); ?></p>
        <?php
    }, 'wxp_salgsfordele_page_desktop', 'wxp_salgsfordele_desktop');
    
    // Desktop ticker toggle
    add_settings_field('wxp_desktop_ticker', __('Animation', 'wxp'), function () {
        $opts = wxp_salgsfordele_get_options();
        ?>
        <label>
            <input type="checkbox" name="wxp_salgsfordele_options[desktop_ticker]" value="JA" <?php checked($opts['desktop_ticker'], 'JA'); ?> />
            <?php esc_html_e('Aktivér scrollende animation', 'wxp'); ?>
        </label>
        <p class="description"><?php esc_html_e('Når deaktiveret, vises teksten statisk centreret', 'wxp'); ?></p>
        <?php
    }, 'wxp_salgsfordele_page_desktop', 'wxp_salgsfordele_desktop');
    
    // Desktop speed
    add_settings_field('wxp_desktop_speed', __('Animation hastighed', 'wxp'), function () {
        $opts = wxp_salgsfordele_get_options();
        ?>
        <input type="range" name="wxp_salgsfordele_options[desktop_speed]" 
               min="5" max="60" step="1" 
               value="<?php echo esc_attr($opts['desktop_speed']); ?>" 
               id="wxp_desktop_speed_slider" />
        <span id="wxp_desktop_speed_value"><?php echo esc_html($opts['desktop_speed']); ?></span> <?php esc_html_e('sekunder', 'wxp'); ?>
        <p class="description"><?php esc_html_e('Hvor lang tid det tager for én fuld omgang (5-60 sekunder)', 'wxp'); ?></p>
        <script>
            document.getElementById('wxp_desktop_speed_slider').addEventListener('input', function() {
                document.getElementById('wxp_desktop_speed_value').textContent = this.value;
            });
        </script>
        <?php
    }, 'wxp_salgsfordele_page_desktop', 'wxp_salgsfordele_desktop');
    
    // Desktop padding
    add_settings_field('wxp_desktop_padding', __('Afstand mellem tekster', 'wxp'), function () {
        $opts = wxp_salgsfordele_get_options();
        ?>
        <input type="number" name="wxp_salgsfordele_options[desktop_padding]" 
               min="0" max="200" step="1" 
               value="<?php echo esc_attr($opts['desktop_padding']); ?>" 
               class="small-text" /> px
        <p class="description"><?php esc_html_e('Horisontal afstand mellem tekster (0-200 px)', 'wxp'); ?></p>
        <?php
    }, 'wxp_salgsfordele_page_desktop', 'wxp_salgsfordele_desktop');
    
    // Desktop height padding
    add_settings_field('wxp_desktop_height', __('Højde padding', 'wxp'), function () {
        $opts = wxp_salgsfordele_get_options();
        ?>
        <input type="number" name="wxp_salgsfordele_options[desktop_height]" 
               min="0" max="50" step="1" 
               value="<?php echo esc_attr($opts['desktop_height']); ?>" 
               class="small-text" /> px
        <p class="description"><?php esc_html_e('Vertikal padding øverst og nederst (0-50 px)', 'wxp'); ?></p>
        <?php
    }, 'wxp_salgsfordele_page_desktop', 'wxp_salgsfordele_desktop');
    
    // === MOBILE SETTINGS ===
    
    // Mobile background color
    add_settings_field('wxp_mobile_bgfarve', __('Baggrundsfarve', 'wxp'), function () {
        $opts = wxp_salgsfordele_get_options();
        ?>
        <input type="color" name="wxp_salgsfordele_options[mobile_bgfarve]" value="<?php echo esc_attr($opts['mobile_bgfarve']); ?>" />
        <?php
    }, 'wxp_salgsfordele_page_mobile', 'wxp_salgsfordele_mobile');
    
    // Mobile text color
    add_settings_field('wxp_mobile_txtfarve', __('Tekstfarve', 'wxp'), function () {
        $opts = wxp_salgsfordele_get_options();
        ?>
        <input type="color" name="wxp_salgsfordele_options[mobile_txtfarve]" value="<?php echo esc_attr($opts['mobile_txtfarve']); ?>" />
        <?php
    }, 'wxp_salgsfordele_page_mobile', 'wxp_salgsfordele_mobile');
    
    // Mobile font
    add_settings_field('wxp_mobile_font', __('Skrifttype', 'wxp'), function () {
        $opts = wxp_salgsfordele_get_options();
        $fonts = array(
            'Arial, sans-serif' => 'Arial',
            'Helvetica, sans-serif' => 'Helvetica',
            'Georgia, serif' => 'Georgia',
            'Times New Roman, serif' => 'Times New Roman',
            'Courier New, monospace' => 'Courier New',
            'Verdana, sans-serif' => 'Verdana',
            'Trebuchet MS, sans-serif' => 'Trebuchet MS',
            'Impact, sans-serif' => 'Impact',
            'Comic Sans MS, cursive' => 'Comic Sans MS',
        );
        ?>
        <select name="wxp_salgsfordele_options[mobile_font]">
            <?php foreach ($fonts as $value => $label) : ?>
                <option value="<?php echo esc_attr($value); ?>" <?php selected($opts['mobile_font'], $value); ?>>
                    <?php echo esc_html($label); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php
    }, 'wxp_salgsfordele_page_mobile', 'wxp_salgsfordele_mobile');
    
    // Mobile font size
    add_settings_field('wxp_mobile_fontsize', __('Skriftstørrelse', 'wxp'), function () {
        $opts = wxp_salgsfordele_get_options();
        ?>
        <input type="number" name="wxp_salgsfordele_options[mobile_fontsize]" 
               min="10" max="32" step="1" 
               value="<?php echo esc_attr($opts['mobile_fontsize']); ?>" 
               class="small-text" /> px
        <p class="description"><?php esc_html_e('Tekststørrelse (10-32 px)', 'wxp'); ?></p>
        <?php
    }, 'wxp_salgsfordele_page_mobile', 'wxp_salgsfordele_mobile');
    
    // Mobile animation type
    add_settings_field('wxp_mobile_animation', __('Animation type', 'wxp'), function () {
        $opts = wxp_salgsfordele_get_options();
        ?>
        <label>
            <input type="radio" name="wxp_salgsfordele_options[mobile_animation]" value="slide" 
                   <?php checked($opts['mobile_animation'], 'slide'); ?> id="wxp_mobile_animation_slide" />
            <?php esc_html_e('Slide (skift mellem tekster)', 'wxp'); ?>
        </label>
        <br>
        <label>
            <input type="radio" name="wxp_salgsfordele_options[mobile_animation]" value="scroll" 
                   <?php checked($opts['mobile_animation'], 'scroll'); ?> id="wxp_mobile_animation_scroll" />
            <?php esc_html_e('Scroll (kontinuerlig scrolling)', 'wxp'); ?>
        </label>
        <p class="description"><?php esc_html_e('Vælg mellem slide-effekt eller kontinuerlig scrolling', 'wxp'); ?></p>
        <?php
    }, 'wxp_salgsfordele_page_mobile', 'wxp_salgsfordele_mobile');
    
    // Mobile interval (for slide)
    add_settings_field('wxp_mobile_interval', __('Slide interval', 'wxp'), function () {
        $opts = wxp_salgsfordele_get_options();
        ?>
        <input type="number" name="wxp_salgsfordele_options[mobile_interval]" 
               min="2" max="20" step="1" 
               value="<?php echo esc_attr($opts['mobile_interval']); ?>" 
               class="small-text" id="wxp_mobile_interval_field" /> <?php esc_html_e('sekunder', 'wxp'); ?>
        <p class="description"><?php esc_html_e('Hvor lang tid hver tekst vises før skift (2-20 sekunder) - kun for slide animation', 'wxp'); ?></p>
        <?php
    }, 'wxp_salgsfordele_page_mobile', 'wxp_salgsfordele_mobile');
    
    // Mobile speed (for scroll)
    add_settings_field('wxp_mobile_speed', __('Scroll hastighed', 'wxp'), function () {
        $opts = wxp_salgsfordele_get_options();
        ?>
        <input type="range" name="wxp_salgsfordele_options[mobile_speed]" 
               min="5" max="60" step="1" 
               value="<?php echo esc_attr($opts['mobile_speed']); ?>" 
               id="wxp_mobile_speed_slider" />
        <span id="wxp_mobile_speed_value"><?php echo esc_html($opts['mobile_speed']); ?></span> <?php esc_html_e('sekunder', 'wxp'); ?>
        <p class="description"><?php esc_html_e('Hvor lang tid det tager for én fuld omgang (5-60 sekunder) - kun for scroll animation', 'wxp'); ?></p>
        <script>
            document.getElementById('wxp_mobile_speed_slider').addEventListener('input', function() {
                document.getElementById('wxp_mobile_speed_value').textContent = this.value;
            });
        </script>
        <?php
    }, 'wxp_salgsfordele_page_mobile', 'wxp_salgsfordele_mobile');
    
    // Mobile padding
    add_settings_field('wxp_mobile_padding', __('Afstand mellem tekster', 'wxp'), function () {
        $opts = wxp_salgsfordele_get_options();
        ?>
        <input type="number" name="wxp_salgsfordele_options[mobile_padding]" 
               min="0" max="200" step="1" 
               value="<?php echo esc_attr($opts['mobile_padding']); ?>" 
               class="small-text" /> px
        <p class="description"><?php esc_html_e('Horisontal afstand mellem tekster (0-200 px)', 'wxp'); ?></p>
        <?php
    }, 'wxp_salgsfordele_page_mobile', 'wxp_salgsfordele_mobile');
    
    // Mobile height padding
    add_settings_field('wxp_mobile_height', __('Højde padding', 'wxp'), function () {
        $opts = wxp_salgsfordele_get_options();
        ?>
        <input type="number" name="wxp_salgsfordele_options[mobile_height]" 
               min="0" max="50" step="1" 
               value="<?php echo esc_attr($opts['mobile_height']); ?>" 
               class="small-text" /> px
        <p class="description"><?php esc_html_e('Vertikal padding øverst og nederst (0-50 px)', 'wxp'); ?></p>
        <?php
    }, 'wxp_salgsfordele_page_mobile', 'wxp_salgsfordele_mobile');
});

// ---------- Admin: page render ----------
function wxp_salgsfordele_settings_page_render() {
    if (!current_user_can('manage_woocommerce')) {
        return;
    }
    
    // Get plugin data
    $plugin_data = get_plugin_data(__FILE__);
    $version = $plugin_data['Version'];
    $author = $plugin_data['Author'];
    
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <p style="color: #666; font-size: 0.9em; margin-top: 5px;">
            Version <?php echo esc_html($version); ?> | <?php echo esc_html($author); ?>
        </p>
        
        <form method="post" action="options.php">
            <?php settings_fields('wxp_salgsfordele_group'); ?>
            
            <style>
                .wxp-settings-grid {
                    display: grid;
                    grid-template-columns: 1fr 1fr 1fr;
                    gap: 20px;
                    margin-top: 20px;
                }
                .wxp-settings-box {
                    background: #ffffff;
                    border: 1px solid #ddd;
                    border-radius: 8px;
                    padding: 25px;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
                }
                .wxp-settings-box h2 {
                    margin-top: 0;
                    padding-top: 0;
                    font-size: 1.2em;
                    border-bottom: 2px solid #f0f0f0;
                    padding-bottom: 10px;
                    margin-bottom: 15px;
                }
                .wxp-settings-box > p:first-of-type {
                    margin-top: -5px;
                    color: #666;
                    font-size: 0.95em;
                }
                .wxp-settings-box .form-table {
                    margin-top: 15px;
                    background: transparent;
                }
                .wxp-settings-box .form-table th {
                    display: block;
                    width: 100%;
                    padding: 0 0 5px 0;
                    font-weight: 600;
                }
                .wxp-settings-box .form-table td {
                    display: block;
                    width: 100%;
                    padding: 0 0 20px 0;
                }
                .wxp-settings-box .form-table tr {
                    display: block;
                    margin-bottom: 10px;
                }
                .wxp-settings-box .form-table input[type="text"],
                .wxp-settings-box .form-table input[type="url"],
                .wxp-settings-box .form-table input[type="number"],
                .wxp-settings-box .form-table select {
                    width: 100%;
                    max-width: 100%;
                }
                .wxp-settings-box .form-table input[type="range"] {
                    width: calc(100% - 80px);
                }
                @media (max-width: 1400px) {
                    .wxp-settings-grid {
                        grid-template-columns: 1fr;
                    }
                }
            </style>
            
            <div class="wxp-settings-grid">
                <div class="wxp-settings-box">
                    <?php do_settings_sections('wxp_salgsfordele_page_general'); ?>
                </div>
                <div class="wxp-settings-box">
                    <?php do_settings_sections('wxp_salgsfordele_page_desktop'); ?>
                </div>
                <div class="wxp-settings-box">
                    <?php do_settings_sections('wxp_salgsfordele_page_mobile'); ?>
                </div>
            </div>
            
            <?php submit_button(__('Gem ændringer', 'wxp')); ?>
        </form>
    </div>
    <?php
}

// ---------- Frontend: render ----------
function wxp_salgsfordele() {
    $opts = wxp_salgsfordele_get_options();
    
    if ($opts['vis'] !== 'JA') {
        return;
    }
    
    // Collect non-empty texts
    $texts = array();
    for ($i = 1; $i <= 8; $i++) {
        $key = 'text_' . $i;
        if (!empty($opts[$key])) {
            $texts[] = $opts[$key];
        }
    }
    
    if (empty($texts)) {
        return;
    }
    
    $is_mobile = wp_is_mobile();
    
    // Get device-specific settings
    if ($is_mobile) {
        $bg = esc_attr($opts['mobile_bgfarve']);
        $fg = esc_attr($opts['mobile_txtfarve']);
        $font = esc_attr($opts['mobile_font']);
        $fontsize = intval($opts['mobile_fontsize']);
        $animation = $opts['mobile_animation'];
        $speed = intval($opts['mobile_speed']);
        $interval = intval($opts['mobile_interval']);
        $padding = intval($opts['mobile_padding']);
        $height = intval($opts['mobile_height']);
    } else {
        $bg = esc_attr($opts['desktop_bgfarve']);
        $fg = esc_attr($opts['desktop_txtfarve']);
        $font = esc_attr($opts['desktop_font']);
        $fontsize = intval($opts['desktop_fontsize']);
        $ticker_enabled = ($opts['desktop_ticker'] === 'JA');
        $speed = intval($opts['desktop_speed']);
        $padding = intval($opts['desktop_padding']);
        $height = intval($opts['desktop_height']);
    }
    
    // URL settings
    $url_enabled = ($opts['url_enabled'] === 'JA');
    $url = !empty($opts['url']) ? esc_url($opts['url']) : '';
    $has_link = $url_enabled && !empty($url);
    
    // Use static IDs for WP Rocket compatibility
    $banner_id = 'wxp-salgsfordele-banner';
    $animation_name = 'wxp-scroll-animation';
    ?>
    <style>
        #<?php echo $banner_id; ?> {
            background: <?php echo $bg; ?>;
            padding: <?php echo $height; ?>px 0;
            overflow: hidden;
            position: relative;
        }
        #<?php echo $banner_id; ?> a,
        #<?php echo $banner_id; ?> .wxp-no-link {
            display: block;
            text-decoration: none;
            color: <?php echo $fg; ?>;
            font-family: <?php echo $font; ?>;
            font-size: <?php echo $fontsize; ?>px;
        }
        #<?php echo $banner_id; ?> .wxp-no-link {
            cursor: default;
        }
        
        <?php if ($is_mobile) : ?>
        /* Mobile behavior */
        <?php if ($animation === 'scroll') : ?>
        /* Mobile: scroll animation */
        .wxp-banner-wrapper {
            display: flex;
            width: fit-content;
        }
        .wxp-banner-scroll {
            display: flex;
            white-space: nowrap;
            animation: <?php echo $animation_name; ?> <?php echo $speed; ?>s linear infinite;
        }
        .wxp-banner-item {
            padding: 0 <?php echo $padding; ?>px;
            flex-shrink: 0;
        }
        @keyframes <?php echo $animation_name; ?> {
            0% { transform: translateX(0); }
            100% { transform: translateX(-33.333333%); }
        }
        <?php else : ?>
        /* Mobile: slide transition between texts */
        .wxp-banner-scroll {
            position: relative;
            min-height: 1.5em;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .wxp-banner-item {
            position: absolute;
            width: 100%;
            text-align: center;
            opacity: 0;
            transform: translateX(100%);
            transition: opacity 0.5s ease, transform 0.5s ease;
            padding: 0 20px;
            box-sizing: border-box;
        }
        .wxp-banner-item.active {
            opacity: 1;
            transform: translateX(0);
            position: relative;
        }
        .wxp-banner-item.exit {
            opacity: 0;
            transform: translateX(-100%);
        }
        <?php endif; ?>
        <?php else : ?>
        /* Desktop behavior */
        <?php if ($ticker_enabled) : ?>
        .wxp-banner-wrapper {
            display: flex;
            width: fit-content;
        }
        .wxp-banner-scroll {
            display: flex;
            white-space: nowrap;
            animation: <?php echo $animation_name; ?> <?php echo $speed; ?>s linear infinite;
        }
        .wxp-banner-item {
            padding: 0 <?php echo $padding; ?>px;
            flex-shrink: 0;
        }
        @keyframes <?php echo $animation_name; ?> {
            0% { transform: translateX(0); }
            100% { transform: translateX(-33.333333%); }
        }
        #<?php echo $banner_id; ?>:hover .wxp-banner-scroll {
            animation-play-state: paused;
        }
        <?php else : ?>
        .wxp-banner-scroll {
            display: flex;
            justify-content: center;
            align-items: center;
            white-space: nowrap;
        }
        .wxp-banner-item {
            padding: 0 <?php echo $padding / 2; ?>px;
        }
        <?php endif; ?>
        <?php endif; ?>
    </style>
    <div id="<?php echo $banner_id; ?>">
        <?php if ($has_link) : ?>
        <a href="<?php echo $url; ?>">
        <?php else : ?>
        <div class="wxp-no-link">
        <?php endif; ?>
            <?php if (($is_mobile && $animation === 'scroll') || (!$is_mobile && $ticker_enabled)) : ?>
            <div class="wxp-banner-wrapper">
                <div class="wxp-banner-scroll" aria-hidden="false">
                    <?php 
                    // Create 3 copies for seamless infinite loop
                    for ($copy = 0; $copy < 3; $copy++) :
                        foreach ($texts as $text) : 
                    ?>
                        <div class="wxp-banner-item"><?php echo esc_html($text); ?></div>
                    <?php 
                        endforeach;
                    endfor;
                    ?>
                </div>
            </div>
            <?php else : ?>
            <div class="wxp-banner-scroll">
                <?php
                if ($is_mobile && $animation === 'slide') {
                    // Mobile slide: show each text individually with rotation
                    $item_index = 0;
                    foreach ($texts as $text) :
                        $active_class = ($item_index === 0) ? ' active' : '';
                    ?>
                        <div class="wxp-banner-item<?php echo $active_class; ?>" data-index="<?php echo $item_index; ?>">
                            <?php echo esc_html($text); ?>
                        </div>
                    <?php
                        $item_index++;
                    endforeach;
                } else {
                    // Desktop static: show all texts once
                    foreach ($texts as $text) :
                    ?>
                        <div class="wxp-banner-item"><?php echo esc_html($text); ?></div>
                    <?php
                    endforeach;
                }
                ?>
            </div>
            <?php endif; ?>
        <?php if ($has_link) : ?>
        </a>
        <?php else : ?>
        </div>
        <?php endif; ?>
    </div>
    
    <?php if ($is_mobile && $animation === 'slide') : ?>
    <script>
    (function() {
        var banner = document.getElementById('<?php echo $banner_id; ?>');
        var items = banner.querySelectorAll('.wxp-banner-item');
        var currentIndex = 0;
        var totalItems = items.length;
        var interval = <?php echo $interval * 1000; ?>; // Convert to milliseconds
        
        function showNext() {
            if (totalItems <= 1) return;
            
            // Mark current as exiting
            items[currentIndex].classList.remove('active');
            items[currentIndex].classList.add('exit');
            
            // Move to next
            currentIndex = (currentIndex + 1) % totalItems;
            
            // Show next
            items[currentIndex].classList.remove('exit');
            items[currentIndex].classList.add('active');
            
            // Clean up exit class after transition
            setTimeout(function() {
                for (var i = 0; i < totalItems; i++) {
                    if (i !== currentIndex) {
                        items[i].classList.remove('exit');
                    }
                }
            }, 500);
        }
        
        // Start rotation
        if (totalItems > 1) {
            setInterval(showNext, interval);
        }
    })();
    </script>
    <?php endif; ?>
    <?php
}

// ---------- Hooks: Generic WordPress hooks for broad theme compatibility ----------
// Display banner at the very top of the page body for all devices

add_action('wp_body_open', 'wxp_salgsfordele', 10);

// Fallback for themes that don't support wp_body_open (WordPress < 5.2)
if (!function_exists('wp_body_open')) {
    function wp_body_open() {
        do_action('wp_body_open');
    }
}

// ALTERNATIVE HOOK OPTIONS (commented out - uncomment if you need different positioning):
// add_action('get_header', 'wxp_salgsfordele', 10);  // Before header loads
// add_action('wp_head', 'wxp_salgsfordele', 999);    // End of <head> section
// add_action('wp_footer', 'wxp_salgsfordele', 1);    // At the bottom of page

// FLATSOME-SPECIFIC HOOKS (commented out - uncomment if using Flatsome theme):
// if (wp_is_mobile()) {
//     add_action('flatsome_before_header', 'wxp_salgsfordele', 10);
// } else {
//     add_action('flatsome_before_header', 'wxp_salgsfordele', 10);
// }