<?php
 
/*
Plugin Name: Last Video Youtube
Plugin URI: http://www.josejavierfm.es/gslv
Description: Widget con el ultimo video de un canal de youtube
Version: 1.2.1
Author: José Javier Fernández Mendoza
Author URI: http://www.josejavierfm.es/
*/
 
/**
 * Función que instancia el Widget
 */
function gslv_create_widget(){    
    include_once(plugin_dir_path( __FILE__ ).'/gslv-widget.php');
    register_widget('gslv_widget');
}
add_action('widgets_init','gslv_create_widget'); 
 

add_action('init', 'gslv_language'); 

function gslv_language() {

        load_plugin_textdomain( 'messages', false, dirname(plugin_basename(__FILE__)).'/languages/' );

}

 
class gslv_widget extends WP_Widget {
 
    function gslv_widget(){
        // Constructor del Widget.
         $widget_ops = array('classname' => 'gslv_widget', 'description' => "Last Video Youtube" );
        $this->WP_Widget('gslv_widget', "Last Video Youtube", $widget_ops);
    }
 
     function getXML($channel_id){
        
        $url=sprintf('https://www.youtube.com/feeds/videos.xml?channel_id=%s', $channel_id);
        $xml="";
        $xml = @simplexml_load_file($url);
        if ($xml==""){
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $data = curl_exec($curl);
            $xml = simplexml_load_string($data);
        }
        
        return $xml;
    }
    function widget($args,$instance){
        // Contenido del Widget que se mostrará en la Sidebar

        $id = "";
        $idvideo="";
        $channel_id = $instance["gslv_idcanal"];
        $ancho = $instance["gslv_ancho"];
        $unidad = $instance["gslv_tipounidad"];
        $titulo= $instance["gslv_titulo"];
        if ($ancho==""){$ancho=100;}
        $idvideo= $instance["gslv_idvideo"];
        if ($idvideo!=""){
            $id=$idvideo;
        }

        if ($channel_id!="" && $idvideo==""){
            $xml = $this->getXML($channel_id);

            if ($xml->entry[0] && !empty($xml->entry[0]->children('yt', true)->videoId[0])){
                $id = $xml->entry[0]->children('yt', true)->videoId[0];
            }
        }
       
         echo $before_widget;    
        ?>
        <aside id='gslv_widget' class='widget mpw_widget'>
            <? if ($titulo!=""){?>
            <a href="https://www.youtube.com/channel/<?=$channel_id?>" target="_blank">
                <h3 class='widget-title'><?=$titulo?></h3>
            </a>
            <?}else{?>
                <h3 class='widget-title'><?=__("ultimo_video_titulo", "messages")?></h3>
            <?}?>
            <? 
            if ($channel_id!=""){
                if ($id!=""){
                    echo '<iframe style="width:'.$ancho.$unidad.' !important;"';
                    
                    echo ' src="https://www.youtube.com/embed/'.$id.'" frameborder="0" allowfullscreen></iframe>';
                }else{
                ?>
                    <p><?=__("no_video", "messages")?></p>
                <?}
            }else{
            ?>
                <p><?=__("canal_sin_configurar", "messages")?></p>
            <?}?>
             
        </aside>
        <?php
        echo $after_widget;
    }
 
    function update($new_instance, $old_instance){
        // Función de guardado de opciones   
         $instance = $old_instance;
        $instance["gslv_idcanal"] = strip_tags($new_instance["gslv_idcanal"]);
        if (is_numeric($new_instance["gslv_ancho"])){
            $instance["gslv_ancho"] = strip_tags($new_instance["gslv_ancho"]);
        }else{
             $instance["gslv_ancho"]=100;
        }
        $instance["gslv_tipounidad"] = strip_tags($new_instance["gslv_tipounidad"]);
        $instance["gslv_idvideo"] = strip_tags($new_instance["gslv_idvideo"]);
        $instance["gslv_titulo"] = strip_tags($new_instance["gslv_titulo"]);
        // Repetimos esto para tantos campos como tengamos en el formulario.
        return $instance;
    }
 
    function form($instance){
        // Formulario de opciones del Widget, que aparece cuando añadimos el Widget a una Sidebar
         ?>
         <p>
            <label for="<?php echo $this->get_field_id('gslv_titulo'); ?>"><?=__("titulo", "messages")?></label>
            <label style='font-size:0.7em;color:#575757;'><?=__("gslv_override", "messages")?>&quot;<?=__("ultimo_video_titulo", "messages")?>&quot;</label>
            <input class="widefat" id="<?php echo $this->get_field_id('gslv_titulo'); ?>" name="<?php echo $this->get_field_name('gslv_titulo'); ?>" type="text" value="<?php echo esc_attr($instance["gslv_titulo"]); ?>" />
         </p>
         <p>
            <label for="<?php echo $this->get_field_id('gslv_idcanal'); ?>"><?=__("id_del_canal", "messages")?></label>
            <label style='font-size:0.7em;color:#a03333;'><?=__("gslv_obligatorio", "messages")?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('gslv_idcanal'); ?>" name="<?php echo $this->get_field_name('gslv_idcanal'); ?>" type="text" value="<?php echo esc_attr($instance["gslv_idcanal"]); ?>" />
         </p>
         <p>
            <label for="<?php echo $this->get_field_id('gslv_idvideo'); ?>"><?=__("id_video_portada", "messages")?></label>
            <br><label style='font-size:0.7em;color:#575757;'><?=__("id_video_portada_legend", "messages")?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('gslv_idvideo'); ?>" name="<?php echo $this->get_field_name('gslv_idvideo'); ?>" type="text" value="<?php echo esc_attr($instance["gslv_idvideo"]); ?>" />
         </p>
         <p>
            <label for="<?php echo $this->get_field_id('gslv_ancho'); ?>"><?=__("gslv_ancho", "messages")?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('gslv_ancho'); ?>" name="<?php echo $this->get_field_name('gslv_ancho'); ?>" type="text" value="<?php echo esc_attr($instance["gslv_ancho"]); ?>" />
         </p>
         <p>
            <label for="<?php echo $this->get_field_id('gslv_tipounidad'); ?>"><?=__("gslv_tipounidad", "messages")?></label>
            <select id="<?php echo $this->get_field_id('gslv_tipounidad'); ?>" name="<?php echo $this->get_field_name('gslv_tipounidad'); ?>" class="widefat" style="width:100%;"> 
                <option <?php selected( $instance['gslv_tipounidad'], '%'); ?> value="%">%</option>
                <option <?php selected( $instance['gslv_tipounidad'], 'px'); ?> value="px">px</option>
                
            </select>
           
         </p>  
         <?php
    }    
} 
 

?>