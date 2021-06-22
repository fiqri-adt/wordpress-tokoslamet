<?php
/**
 * Admin Settings
 *
 * @author AgenWebsite
 * @package WooCommerce Kode Pembayaran
 * @since 1.1.2
 */

if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

if ( ! class_exists( 'KodePembayaranSettings' ) ):

class KodePembayaranSettings{

    private $submenu;
    private $options;
    private $usefull_links;

    /**
     * Constructor
     *
     * @return void
     * @since 1.1.2
     */
    public function __construct(){
        $this->set_property();
        $this->init_hooks();
    }

    /**
     * Set Property
     *
     * @return void
     * @since 1.1.2
     */
    public function set_property(){
        $this->submenu = array(
            'woocommerce',
            __('Kode Pembayaran', 'agenwebsite'),
            __('Kode Pembayaran', 'agenwebsite'),
            'manage_options',
            'woocommerce-kode-pembayaran'
        );

        $this->usefull_links = array(
            'documentasi' => array(
                'label' => __('Dokumentasi', 'agenwebsite'),
                'url' => 'http://docs.agenwebsite.com/products/woocommerce-kode-pembayaran/'
            ),
            'bantuan' => array(
                'label' => __('Bantuan', 'agenwebsite'),
                'url' => 'http://agenwebsite.com/support/'
            )
        );
    }

    /**
     * Init Hooks
     *
     * @return void
     * @since 1.1.2
     */
    public function init_hooks(){
        add_action( 'init', array( $this, 'set_default_options' ) );
        add_action( 'init', array( $this, 'init_tabs' ) );
        add_action( 'init', array( $this, 'save_default_options' ) );
        add_action( 'agenwebsite_tools_before_tabs', array( $this, 'banner_upgrade' ) );
    }

    /**
     * Set default options
     *
     * @return void
     * @since 1.1.2
     */
    public function set_default_options(){
        $this->options = $this->options();
    }

    /**
     * Init Tabs
     *
     * @return void
     * @since 1.1.2
     */
    public function init_tabs(){
        $this->tabs = new wp_aw_admin(
            $this->submenu, // submenu setting
            $this->options, // options tab, section and fields
            'kode-pembayaran-group', // option name of group settings
            'kode-pembayaran', // option name
            KODE_PEMBAYARAN_URL, // plugin url
            'agenwebsite', // localize
            $this->usefull_links // additional settings
        );
    }

    /**
     * Save default options
     *
     * @return void
     * @since 1.1.2
     */
    public function save_default_options(){
        foreach( $this->options() as $tab ){
            foreach( $tab['sections'] as $section ){
                foreach( $section['fields'] as $field => $data){
                    if( isset($data['std']) && isset( $field ) ){
                        add_option($field, $data['std']);
                    }
                }
            }
        }        
    }

    /**
     * Banner Upgrade
     *
     * @return void
     * @since 1.1.2
     */
    public function banner_upgrade(){
        global $pagenow;
        if( $pagenow == 'admin.php' && isset( $_GET['page'] ) && $_GET['page'] == 'woocommerce-kode-pembayaran' ){
            $img_src = KODE_PEMBAYARAN_URL . '/assets/images/upgrade-728x90.png';
        ?>
            <a href="http://agenwebsite.com/products/woocommerce-kode-pembayaran" title="Upgrade Plugin" target="_blank"><img src="<?php echo $img_src;?>" width="728px" height="90px" /></a>
        <?php
        }
    }

    /**
     * Generate Radio
     *
     * @return void
     * @since 1.1.2
     */
    public function radio($data){

        $defaults = array(
			'title'             => '',
			'label'             => '',
			'class'             => '',
			'css'               => '',
            'disabled'          => false,
			'type'              => 'text',
			'description'       => '',
            'std'               => '',
            'id'                => '',
            'label_for'         => '',
            'options'           => array(),
			'custom_attributes' => array()
		);

        $data = wp_parse_args( $data, $defaults );
        $value = get_option( $data['id'], $data['std'] );

        ob_start();
        ?>
        <fieldset><ul class="pola_kode">
        <?php if( !empty($data['options']) ):?>
            <?php foreach($data['options'] as $key => $label): if($key!=='acak') $data['disabled'] = true;?>
            
            <li class="<?php echo $class;?>">
                <input type="radio" name="<?php echo $data['id'];?>" id="<?php echo $key;?>" style="<?php echo $data['css'];?>" value="<?php echo $key;?>" <?php checked($value, $key, true);?> <?php disabled($data['disabled'], true);?> />
                <label for="<?php echo $key;?>"><?php echo $label;?></label>
            </li>
            <?php endforeach;?>
        <?php endif;?>
        </ul></fieldset>
        <?php
        return ob_get_clean();        
    }

    /**
     * Options
     *
     * @return array
     * @since 1.1.2
     */
    public function options(){
        $options = array(
            'general' => array(
                'label' => __( 'General', 'agenwebsite' ),
                'sections' => array(
                    'general' => array(
                        'title' => '',
                        'fields' => array(
                            'wc_kode_pembayaran_enable' => array(
                                'title' => __('Aktifkan/Nonaktifkan', 'agenwebsite'),
                                'description' => __('Aktifkan WooCommerce Kode Pembayaran.', 'agenwebsite'),
                                'type' => 'checkbox',
                                'std' => '1'
                            ),
                            'wc_kode_pembayaran_judul' => array(
                                'title' => __( 'Label', 'agenwebsite' ),
                                'description' => __('Ubah label kode pembayaran sesuai dengan yang Anda inginkan.', 'agenwebsite'),
                                'type' => 'text',
                                'std' => 'Kode Pembayaran'
                            ),
                            'wc_kode_pembayaran_metode_biaya' => array(
                                'title' => __( 'Metode Biaya', 'agenwebsite' ) . ' <span>Full Version</span>',
                                'label' => __( 'Metode Pengurangan', 'agenwebsite' ),
                                'description' => __('Aktifkan untuk menggunakan metode pengurangan dalam kalkulasi order total.', 'agenwebsite'),
                                'type' => 'checkbox',
                                'std' => '0',
                                'class' => 'for-full-user',
                                'disabled' => true
                            ),
                            'wc_kode_pembayaran_range_number' => array(
                                'title' => __( 'Range Number', 'agenwebsite' ) . ' <span>Full Version</span>',
                                'description' => __('Masukkan range number untuk diacak. contoh 200-500. <b>Hanya untuk Pola Kode Nomor Acak.</b></b><br>minimal: 100<br>maksimal: 999<br>default: 100-999','agenwebsite'),
                                'type' => 'text',
                                'std' => '100-999',
                                'disabled' => true,
                                'class' => 'for-full-user'
                            ),
                            'wc_kode_pembayaran_pola_kode' => array(
                                'title' => __('Pola Kode', 'agenwebsite'),
                                'description' => '',
                                'type' => array( $this, 'radio' ),
                                'std'  => 'acak',
                                'class' => 'for-full-user',
                                'disabled' => true,
                                'options' => array(
                                    'acak' => 'Nomor Acak',
                                    'hp' => 'Nomor HP <span>Full Version</span>',
                                    'pos' => 'Kode POS <span>Full Version</span>'
                                )
                            )
                        )
                    )
                )
            )
        );

        return $options;
    }

}

endif;