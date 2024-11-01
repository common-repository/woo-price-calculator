<?php
/**
 * @package AWS Price Calculator
 * @author Enrico Venezia
 * @copyright (C) Altos Web Solutions Italia
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
**/

namespace WSF\Helper;

/*AWS_PHP_HEADER*/

use WSF\Helper\FrameworkHelper;

class EcommerceHelper {
    
    var $wsf;

    public function __construct(FrameworkHelper $wsf) {
        $this->wsf = $wsf;
        
        $this->databaseHelper    = $this->wsf->get('\\WSF\\Helper', true, 'awsframework/Helper', 'DatabaseHelper', array($this->wsf));
        
        if($this->getTargetEcommerce() == "hikashop"){
            if(!@include_once(rtrim(JPATH_ADMINISTRATOR,DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_hikashop'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php')){ return false; }
        }
    }
    /*
     * Ritorna il prezzo in formato WooCommerce
     */
    public function get_price($price){
        if($this->getTargetEcommerce() == "woocommerce"){
            return wc_price($price);
        }else if($this->getTargetEcommerce() == "hikashop"){
            $currencyHelper = hikashop_get('class.currency');
            $mainCurrency   = $currencyHelper->mainCurrency();

            return $currencyHelper->format($price, $mainCurrency);
        }
        
    }

    public function getPriceFormatForJs(){
        return utf8_encode(htmlentities($this->get_price(9999.1111111111)));
    }
    
    /*
     * Ritorna la valuta WooComerce utilizzata
     */
    public function get_currency_symbol(){
            return html_entity_decode(get_woocommerce_currency_symbol());
    }

    /*
     * Ritorna il formato del prezzo WooCommerce utilizzato
     */
    public function get_price_format(){
            return get_woocommerce_price_format();
    }

    /*
     * Ritorna l'ID del carrello corrente
     */
    public function get_current_cartid(){
            global $woocommerce;
            $items = $woocommerce->cart->get_cart();

            foreach($items as $key => $value){
                    return $key;
            }
    }

    /*
     * Ritorna un prodotto di WooCommerce utilizzando l'ID
     */
    public function getProductById($product_id){
        if($this->getTargetEcommerce() == "woocommerce"){
            $obj_product = new \WC_Product($product_id);
            return $this->getProductArrayFromWooCommerce($obj_product);
        }else if($this->getTargetEcommerce() == "hikashop"){
            $productClass        = \hikashop_get('class.product');
            return $this->getProductArrayFromHikashop($productClass->getProduct($product_id));
        }
    }
    
    public function getProductsByIds($productIds = array()){
        
        $products   = array();
        
        foreach($productIds as $productId){
            try{
                $products[$productId]     = $this->getProductById($productId);
            
            /* If products was deleted by WooCommerce an 'Invalid product' exception will be generated */
            }catch(\Exception $ex){
                
            }
        }
        
        return $products;
    }

    /*
     * Ritorna la lista di tutti i prodotti WooCommerce
     */
    public function getProducts($productsPerPage = 10, $start = 0, $orderBy = null, $orderDir = null, $search = null){
        
        $products   = array();
        
        if($this->getTargetEcommerce() == "woocommerce"){
            
            $args = array( 
                'post_type'         => 'product', 
                'posts_per_page'    => $productsPerPage,
                'offset'            => $start,
            );
            
            if($orderBy != null){
                $args['orderby']    = $orderBy;
                $args['order']      = $orderDir;
            }else{
                $args['orderby']    = 'id';
                $args['order']      = 'DESC';
            }
            
            if($search != null){
                $args['s']          = $search;
            }
            
            $loop               = new \WP_Query($args);
            $totalProducts      = $loop->found_posts;
            $count              = $loop->post_count;
            
            while ( $loop->have_posts() ) : $loop->the_post(); 
                global $product; 
                $products[] = $this->getProductArrayFromWooCommerce($product);
                
            endwhile; 
                wp_reset_query();
               
       }else if($this->getTargetEcommerce() == "hikashop"){
           $productClass        = \hikashop_get('class.product');      
           $productClass->getProducts(null, 'id');

           $bufferedProducts	= array();
           foreach($productClass->products as $bufferedProduct){
                $bufferedProducts[]		= $this->getProductArrayFromHikashop($bufferedProduct);
           }
			
           for($i = $start; $i < $start + $productsPerPage; $i++){
                $products[]      = $bufferedProducts[$i];
            }

           $count               = count($products);
           $totalProducts       = count($bufferedProducts);
        }
       
        return array(
            'products'          => $products,
            'totalProducts'     => $totalProducts,
            'count'             => $count,
        );
        
    }
    
    /*
     * Converte il prodotto di Hikashop, in un prodotto universalmente comprensibile
     */
    public function getProductArrayFromHikashop($product){
        return array(
                   'id'     => $product->product_id,
                   'name'   => $product->product_name,
                   'price'  => $product->product_sort_price,
        );
    }
    
    /*
     * Converte il prodotto di WooCommerce, in un prodotto universalmente comprensibile
     */
    public function getProductArrayFromWooCommerce($product){
        return array(
            'id'        => $product->get_id(),
            'name'      => $product->get_title(),
            'price'     => $product->get_regular_price(),
        );
    }
    
    /* Genera un prodotto in memoria */
    public function getProductArray($id, $name, $price){
        return array(
            'id'        => $id,
            'name'      => $name,
            'price'     => $price,
        );
    }
    
    public function getProductCategories(){

        $result     = array();
        
        if($this->getTargetEcommerce() == "woocommerce"){
            foreach (get_terms('product_cat', array('hide_empty' => 0, 'parent' => 0)) as $each) {
                $result     = $result + $this->getProductCategoriesRecursive($each->taxonomy, $each->term_id);
            }
        }else if($this->getTargetEcommerce() == "hikashop"){
            $categoryClass        = \hikashop_get('class.category');   
            $categoryClass->getCategories(null);
            
            /* TODO */
            return array();
            
            foreach($categoryClass as $category){
                print_r($category);
            }
            
        }
        
        return $result;
    }
    

    function getProductCategoriesRecursive($taxonomy = '', $termId, $separator='', $parent_shown = true){

        $args   = array(
            'hierarchical'      => 1,
            'taxonomy'          => $taxonomy,
            'hide_empty'        => 0,
            'orderby'           => 'id',
            'parent'            => $termId,
        );
        
        $term           = get_term($termId , $taxonomy); 
        $result         = array();
        
        if ($parent_shown) {
            //$output                 = $term->name . '<br/>'; 
            $result[$term->term_id]    = $term->name;
            $parent_shown           = false;
        }
        
        $terms          = get_terms($taxonomy, $args);
        $separator      .= $term->name . ' > ';  

        if(count($terms) > 0){            
            /*
             * $term->term_id
             * $category->term_id
             */
            foreach ($terms as $term) {
                //$output .=  $separator . $term->name . " " . $term->slug . '<br/>';
                $result[$term->term_id]        = $separator . $term->name;
                
                //$output .=  $this->getProductCategoriesRecursive($taxonomy, $term->term_id, $separator, $parent_shown);
                $result  = $result + $this->getProductCategoriesRecursive($taxonomy, $term->term_id, $separator, $parent_shown);
            }
        }
        
        return $result;
    }
    
    function getCategoryProductsByCategorySlug($productCategorySlug = null){
        /* Using Raw MySQL Query instead of \WP_Query which throw a "Allowed memory" error */

        $rows = $this->databaseHelper->getResults("SELECT object_id FROM [prefix]terms
                        LEFT JOIN [prefix]term_taxonomy ON [prefix]term_taxonomy.term_id = [prefix]terms.term_id
                        LEFT JOIN [prefix]term_relationships ON [prefix]term_relationships.term_taxonomy_id = [prefix]terms.term_id
                        WHERE slug = :slug
                        AND taxonomy = 'product_cat';", array(
                                'slug'	=> $productCategorySlug,
                        ));

        $products   = array();
        foreach($rows as $row){
                        $products[]		= $row->object_id;
        }

        return $products;
    }
    
    function getCategoryProductsByCategoryId($categoryId = null){
        $term = get_term($categoryId, 'product_cat');
        
        if(empty($term)){
            return array();
        }
        
        if($categoryId == null){
            $slug   = null;
        }else{
            $slug = $term->slug;
        }
        
        return $this->getCategoryProductsByCategorySlug($slug);
    }
    
    public function getTargetEcommerce(){
        $license   = file_get_contents($this->wsf->getPluginPath("resources/data/ecommerce.bin", true));

        return trim($license);
    }
    
    /*
     * Se true si tratta della pagina prodotto, altrimenti no
     */
    public function isProduct(){
        if($this->getTargetEcommerce() == "woocommerce"){
            return is_product();
        }else if($this->getTargetEcommerce() == "hikashop"){
            $option     = $this->wsf->requestValue('option');
            $ctrl       = $this->wsf->requestValue('ctrl');
            $task       = $this->wsf->requestValue('task');

            if($option == "com_hikashop" && $ctrl == "product" && $task == "show"){
                return true;
            }
            
            return false;
        }
    }
    
    /*
     * Se true indica che ci troviamo nel carrello, altrimenti no
     */
    public function isCart(){
        if($this->getTargetEcommerce() == "woocommerce"){
            return is_cart();
        }else if($this->getTargetEcommerce() == "hikashop"){
            /* TODO: Non riesco ad identificare in maniera ancora più precisa se la pagina contiene il carrello */
            if($this->isProduct() == true){
                return false;
            }
            
            return true;
        }
    }
    
    public function getPageType(){
        if($this->isCart() == true){
            return "cart";
        }
        
        if($this->isProduct() == true){
            return "product";
        }
        
        return null;
    }
    
    /*
     * Svuota il carrello
     */
    public function emptyCart(){
        if($this->getTargetEcommerce() == "woocommerce"){
            WC()->cart->empty_cart();
        }else if($this->getTargetEcommerce() == "hikashop"){
            hikashop_nocache();

            $cartClass  = \hikashop_get('class.cart');
            $cart_id    = $cartClass->getCurrentCartId();
            
            $cartClass->delete($cart_id);
        }
    }
    
    /* TODO-LATER */
    /*
     * Ritorna il separatore dei decimali configurato nell'ecommerce
     */
    
    public function getDecimalSeparator(){
        if($this->getTargetEcommerce() == "woocommerce"){
            return wc_get_price_decimal_separator();
        }else if($this->getTargetEcommerce() == "hikashop"){
            
        }
        
    }
    
    /* TODO-LATER */
    /*
     * Ritorna il separatore delle migliaglia configurato nell'ecommerce
     */
    public function getThousandSeparator(){
        
        if($this->getTargetEcommerce() == "woocommerce"){
            return wc_get_price_thousand_separator();
        }else if($this->getTargetEcommerce() == "hikashop"){
            
        }
    }
    
    /* TODO-LATER */
    /*
     * Ritorna il numero di decimali configurato nell'ecommerce
     */
    public function getDecimals(){
        if($this->getTargetEcommerce() == "woocommerce"){
            return wc_get_price_decimals();
        }else if($this->getTargetEcommerce() == "hikashop"){
            
        }
    }
    
    /*
     * Get the cart URL
     */
    public function getCartUrl(){
        return wc_get_cart_url();
    }
    


}