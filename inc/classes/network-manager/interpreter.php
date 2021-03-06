<?php
//simply a function that gives our network manager class useable results
//this converts our simple xml object to a basic array, more easy to work with
function sx_array($obj){
	$arr = (array)$obj;
	if(empty($arr)){
		$arr = "";
	} else {
		foreach($arr as $key=>$value){
			if(!is_scalar($value)){
				$arr[$key] = sx_array($value);
			}
		}
	}
	
	return $arr;
}
function symbiostock_interpret_results( $symbiostock_xml_results )
{    
	//testing, uncomment next lines
	//header( "Content-Type: text/plain" ); 
	//echo $symbiostock_xml_results;
	
    $results = new SimpleXmlElement( $symbiostock_xml_results );
     
	$results = sx_array($results);
	    
    return $results;
    
}
//this lists the hidden inputs, which contains info for modal windows
function symbiostock_list_attr_inputs($count, $image){
	
	$meta_values = array(
		'price_bloggee',
		'price_small',
		'price_medium',
		'price_large',
		'price_vector',
		'price_zip',
		'discount_percent',
		'exclusive',
		'symbiostock_preview',
		'symbiostock_transparency',
		'size_eps',
		'size_zip',
		'extensions',
		'title',
		'permalink', 
		'author',
	);
	foreach($meta_values as $value){
		$id_key = 'n' . $count . '_' . $image['id'] . '_' .$value;
		
		//this huge if statement should be worked over to something better and more comprehensive
		//I tried a switch statement but got nothing better
		
		if ($value == 'extensions' && !empty($image['extensions']['ext'])){
			
			if(is_array($image['extensions']['ext'])){
				$info = implode(', ', $image['extensions']['ext']);
			}
			} elseif(isset($image[$value]) && is_array($image[$value])){
			
			$info = implode(', ', $image[$value]);
			
			} elseif (isset($image[$value]) && is_string($image[$value])) {
				
			$info = $image[$value];
				
			} else {$info = '';}
		?>
       
		<input type="hidden" id="<?php echo $id_key; ?>" name="<?php echo $id_key; ?>" value="<?php echo $info; ?>" />
		<?php
				
		}
	
			if(is_array($image['size_info'])){
			
			foreach($image['size_info'] as $size => $size_info){
				$id_key = 'n' . $count . '_' . $image['id'] . '_' . $size;
				$size_attr = $size_info['width'] . '×' . $size_info['height'] . 'px';
				
				?>                
<input type="hidden" id="<?php echo $id_key; ?>" name="<?php echo $id_key; ?>" value="<?php echo $size_attr; ?>" />
        <?php
				
			}
		}
	}
//this function generates the controls and buttons that show under each search result\
//it seemed appropriate to have a separate function for it in case things got more elaborate
function symbiostock_hover_controls($count, $id, $permalink){
	
	$id = 'n' . $count . '_' . $id;
	
	?><br />
    <span class="sscntrl">
        <span>
            <a class="modal_activate" id="<?php echo $id . '_details'; ?>" title="Preview" data-toggle="modal" href="#symbiostock_display_preview" ><i class="icon-zoom-in">&nbsp;</i></a>
            <a id="<?php echo $id . '_goto'; ?>" title="Go to royalty free image." href="<?php echo  $permalink; ?>"><i class="icon-share-alt">&nbsp;</i></a>
        </span> 
    </span>     
	<?php
}
function symbiostock_display_pagination($pagination, $results, $position, $size){
	
	?><div class="symbiostock_pagination pagination-<?php echo $position; ?> pagination <?php echo $size; ?>">
        <div class="pagination">            
            <ul>
            <?php
            //display pagination
            if(!empty($pagination)){
                
                foreach($pagination as $page_result){
                    
                    echo '<li>' . $page_result . '</li>';
                    
                    }		
                
                }
            ?>
            <li class="disabled">
            <span><?php echo 'Results: ' . $results; ?></span>
            </li>            
            </ul>
        </div>
</div> <?php
	}
	
function symbiostock_build_html_results($results, $network_search, $site_count = 0){
	//site_count variable is simply the site's position on the page/loop. 
	//if this is an incoming request, we alter the page_count according to $_POST['symbiostock_site_order'] so that our 
	//friend's page handle's it properly.
	
	if(!isset($results['image'])){
		if($network_search != true): 
		?><p>No results found on this site.</p>
		
		
		<div class="hero-unit">
            <h2>No Results found. Try browsing the categories. Maybe you will find some hidden gems!</h2>
            <?php
           
           //list terms in a given taxonomy using wp_list_categories (also useful as a widget if using a PHP Code plugin)
            $taxonomy     = 'image-type';
            $orderby      = 'name'; 
            $show_count   = 1;      // 1 for yes, 0 for no
            $pad_counts   = 1;      // 1 for yes, 0 for no
            $hierarchical = 1;      // 1 for yes, 0 for no
            $title        = '<h2 class="muted">Image Categories</h2><hr />';
            
            $args = array(
              'taxonomy'     => $taxonomy,
              'orderby'      => $orderby,
              'show_count'   => $show_count,
              'pad_counts'   => $pad_counts,
              'hierarchical' => $hierarchical,
              'title_li'     => $title,
             
            );
            ?>
            <ul>
                <?php wp_list_categories( $args ); ?>
            </ul>
        </div>
		
		<?php
		endif;
		
		return;
		}
	
	
		
		
	if(isset($_POST['symbiostock_site_order'])){
		
		$site_count = $_POST['symbiostock_site_order'];
		
		}
	
	if($network_search == true): 
	
	$network_info = $results['network_info'];
	
	//validate incoming network attributes
	//about page
			
		
	?><div id="network_site_<?php echo $site_count; ?>" class="network_results row-fluid">
        <div class="span12 well well-small network_results_header">            
            <?php 
            echo symbiostock_csv_symbiocard_network_results(symbiostock_NETDIR . symbiostock_website_to_key($network_info['url']) . '.csv');
            ?>            
        </div>
        <div class="network_results_container"><!--network_results_container-->
        <?php	
        endif;
        
        //check and set pagination results	
        if(isset($results['pagination']) && is_array($results['pagination']) && array_key_exists('page', $results['pagination']) && !empty($results['pagination']['page'])){
							
			$pagination = $results['pagination']['page'];	
					
			$paginate = true;
        } else {
        
			$pagination = array();	
			$paginate = false;
        }	
        
        ?><div class="results_info">
        <?php 
        if($paginate == true && $network_search == false){
            symbiostock_display_pagination($pagination, $results['total_results'], 'right', 'pagination-small');
        } elseif ($network_search == false){
        
            echo '<span>Results: ' . $results['total_results'] . '</span>';
        }
        ?>
        </div>
        <?php	
        
        
        $invis_tag = array(	);
        
        $total_results =  $results['total_results'];
        
        //check and set image results
        if(is_array($results) && array_key_exists('image', $results) && !empty($results['image'])){
                        
        	$image_results = $results['image'];	
        
        } else {
        
        	return '<p>No results found.</p>';
        
        }
        
        
        //if our array was created with only one item, we have to modify it so it can be processed properly	
        //we see if the array depth is altered from what we usually expect
        if(!isset($image_results[0])){
			//if it is, then we shift the array down one level
			$tmp = array();
			$tmp[0]=$image_results;
			
			$image_results = $tmp;
        }
        
        //check and set network info results
        ?> <input type="hidden" id="network_site_<?php echo $site_count; ?>_start_count" value="<?php echo $sscount->count; ?>" /> <?php		
        
        if($network_search == true  && count($image_results) > 6):
        ?>
            <div class="row-fluid">
                <div id="carousel<?php echo $site_count; ?>" class="carousel slide span12">
                     <!-- Dot Tracker -->
                     <ol class="carousel-indicators">
                        <?php
                        $active_dot = 'class="active"';
                        $slides = count($image_results);				
                        $slide_count = 0;
                        while($slide_count < $slides/6):
                        
                        ?><?php 
                        
                        $active_dot = '';
                        $slide_count++;
                        endwhile;
                        ?>
                     </ol>
                    <!-- Carousel items -->
                    <div class="carousel-inner"><!--carousel-inner-->      
                    <?php
                    $carousel_count = 1;
                    $active = true;		
                    endif;
                    $closed = true;
                    foreach($image_results as $image){
                        //carousel
                        if($network_search == true):
                            $active == true ? $class = 'active' : '';		
                            $active == true ? $active = false : $class = '';
                            $carousel_count == 1 && count($image_results) > 6 ? $open_div = '<div class="'.$class.' item"><!--six-per-div-->' : $open_div = ''; 
                            echo $open_div;		
                        endif;
                        
                        global $sscount;
                        
                        if(!is_object($sscount)){
                            //if this is an incoming request, we have to set up counts to mirror the results on that page, using incoming vars
                            $sscount = new results_counter();
                            
                            $sscount->count = $_POST['symbiostock_start_count'];
                                    
                        } 			
                        $count = $sscount->plus_1();
                        
                        if(isset($results['load_ajax'])):			
                        ?> 
                        <img class="" alt="Loading site's results..." src="<?php echo symbiostock_IMGDIR . '/loading-large.gif' ?>" /> 
                        <input class="site_load_ajax" data-search="<?php echo $results['retry_search']; ?>" type="hidden" value="network_site_<?php echo $site_count; ?>" name="load_ajax" />
                        <?php
                        
                        endif;
                        
                        ?>
                        
                        <div id="n<?php echo $count; ?>_<?php echo $image['id'] ?>_image" class="search-result">
                            <a class="search_result_preview" title="<?php echo $image['title'] ?>" href="<?php echo $image['permalink']  ?>">
                              <img alt="image <?php echo $image['id']; ?>" class="search_minipic" src="<?php echo $image['symbiostock_minipic']  ?>" />
                            </a>
                            <?php symbiostock_list_attr_inputs($count, $image); ?>
                            <?php symbiostock_hover_controls($count, $image['id'], $image['permalink']); ?>
                        </div>
                        
                        <?php
                        //carousel
                        if($network_search == true):
                            $carousel_count == 6 ? $close_div = '</div><!--six-per-div-->' : $close_div = '';
                            $carousel_count == 6 && count($image_results) > 6 ? $closed = true : $closed = false;
                            echo $close_div;
                                    
                            $carousel_count++;
                            $carousel_count == 7 ? $carousel_count = 1 : ''; 
                        endif;	
                    }
                    if($closed == false && count($image_results) > 6){
                        echo '</div><!--/six-per-div (if not closed)-->';
                        }
                    if($network_search == true && count($image_results) > 6):
                    ?>
                    <!-- Carousel nav -->
                    </div><!--/carousel-inner-->
                    <a class="carousel-control left" href="#carousel<?php echo $site_count; ?>" data-slide="prev">&lsaquo;</a>
                    <a class="carousel-control right" href="#carousel<?php echo $site_count; ?>" data-slide="next">&rsaquo;</a>
                </div>
            </div><!--row-fluid-->
        <?php	
        endif;
        
        if($network_search == true){
        ?>
        </div><!--/network_results_container--> 
		<?php	
		$position = 'right';
		
		$size = 'small';
		
		//correct the output of our pagination so the user doesn't get led to xml results	
		//extracts all $_GET vars attached to href, everything after "?" and before "'", 
					
		$remove_vars = array(
		'symbiostock_network_search',
		'symbiostock_network_info',
		);
	
	$corrected_pagination = array();
		
	foreach($pagination as $href_link){
		
		$href_link = str_replace('&hellip;', '...', $href_link);	
						
		$a = new SimpleXMLElement( $href_link );
		$link = $a['href'];
					
		
		if(isset($link) && !empty($link)){
			
			if(strstr($link, 'post_type=image')){
				
				$user_link = explode('?', $link);
				$user_link = $network_info['url'] . '?' . remove_query_arg('paged', $user_link[1]);
				$edited_link = str_replace($link, htmlentities($user_link), $href_link);
				$edited_link = str_replace("href", "data-networklink='" . htmlentities($user_link)  . "' href", $edited_link);
				} else {
				
				$user_link = explode('?', $link);
				$user_link = $user_link[0];	
				$edited_link = str_replace($link, htmlentities($user_link), $href_link);
				$edited_link = str_replace("href", "data-networklink='" . htmlentities($link) . "' href", $edited_link);
			}
				
		} else { $edited_link = $href_link;}
		
		array_push($corrected_pagination, $edited_link);
		
		unset($a);
		}
				
		$pagination = $corrected_pagination;	
				
	} else {
		
	$position = 'centered';
	
	$size = 'large';	
		}
	
	if($paginate == true){
		symbiostock_display_pagination($pagination, $results['total_results'], $position, 'pagination-' . $size);
	}
	
	if($network_search == true){ 
		?> </div> <?php 
	}
}
?>