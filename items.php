<?php
defined('ABSPATH') or die;
				$lang = null;
				$is_admin = is_admin();
				$content = '';
				if($is_admin === false) {
					$currentLink = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
					$lang =	get_locale();
					if(!empty($instance['title'])) {
						$title = apply_filters( 'widget_title', $instance['title'] );
					} else {
						$title = null;
					}
					if(!empty($instance['maintitle'])) {
						$maintitle = intval($instance['maintitle']);
					} else {
						$maintitle = null;
					}
					if($maintitle == 1) { // Main category title
						if ( !in_array(0,$parentcats)) {
							$content .= $args['before_title'] . get_cat_name( $parentcats[0] ) . $args['after_title'];
						}
					} else if ($maintitle == 2) { // All main categories titles
						if ( !in_array(0,$parentcats) ) {
							$content .=  $args['before_title'];
							foreach ( $parents as $key => $id) {
								$content .=  get_cat_name( $id ).' ';
							}
							$content .=  $args['after_title'];
						}
					} else if($maintitle == 3) { // Theme and layout title
						$content .= $args['before_title'] . $theme . ' - ' . $layout . $args['after_title'];
					} else if($maintitle == 4) { // Theme title
						$content .= $args['before_title'] . $theme . $args['after_title'];
					} else if($maintitle == 5) { // Layout title
						$content .= $args['before_title'] . $layout . $args['after_title'];
					} else if($maintitle == 6) { // No main title
					} else { // Widget title
						if ( ! empty( $title ) ) {
							$content .= $args['before_title'] . $title . $args['after_title'];
						}
					}
					/*
					Get the autoplay seconds
					*/
					if($autoplay === 0) {
						$autoplayclass = '';
					} else if($autoplay > 0) {
						$autoplayclass = ' mr-widget-autoplay'.$autoplay."s mr-transitionright";
					}
					/*
					Images size inline styles
					*/
					if (!empty($imagemaxwidth)) {
						$addimagemaxwidth = "max-width:".$imagemaxwidth."px;";
					} else {
						$addimagemaxwidth = null;
					}
					if (!empty($imagemaxheight)) {
						if(in_array('background',$imagestypes)) {
							$addimagemaxheight = "min-height:".$imagemaxheight."px;";
						} else {
							$addimagemaxheight = "max-height:".$imagemaxheight."px;";
						}
					} else {
						$addimagemaxheight= '';
					}
					/*
					Array with items to exclude on the frontend.
					*/
					if($excludeinclude === 0) {
						$exclude = $itemselect;
						$include = array();
					} else if($excludeinclude === 1) {
						$exclude = array();
						$include = $itemselect;
					}		
				} else {
					$lang =	'';
					$exclude = array();
					$include = array();
				}
				/*
				Get current active items.
				*/
				$currentitem = null;
				if (is_category()) {
					$currentitem = get_query_var('cat');
				} else if(is_single()) {
					$currentitem = wp_get_post_categories(get_the_ID());
				}
				/*
				Check if categories with no articles should appear or not on the categories array.
				*/
				$hideempty = true;
				if($excludeinclude === 0 && !in_array('noarticles', $taxonomies_groupexclude) || $excludeinclude != 0 || $is_admin === true) {
					$hideempty = false;
				}
				/*
				Get the selected automatic order.
				*/
				if($orderby === 0) { //Creation
					$orderby = 'term_id';
				} else if($orderby === 1) { //Title
					$orderby = 'name';
				} else if($orderby === 3) { //Article count
					$orderby = 'count';
				} else if($orderby === 4) { //Slug
					$orderby = 'slug';
				} else if($orderby === 2) { //Parent (2)
					$orderby = 'parent';
				}
				if($order === 1) {
					$order = 'ASC';
				} else if($order === 0) {
					$order = 'DESC';
				}
				/*
				Join all the previous options into the main array of items.
				*/
					//if ( false === ( $itemlist = get_transient( $widgetid.'_itemlist' ) ) ) {
						if (strpos($contenttypes, 'taxonomy_') !== false) {
							$contenttype = str_replace('taxonomy_','',$contenttypes);
							$itemlist = get_terms(array('taxonomy' => $contenttype,'orderby' => $orderby,'order' => $order,'number' => $itemsnumber, 'hide_empty' => $hideempty, /*'exclude' => $exclude, 'include' => $include,*/ 'lang' => $lang, 'hierarchical' => true,'no_found_rows' => true, 'suppress_filter' => false));
						} else if (strpos($contenttypes, 'posttype_') !== false) {
							if(!$itemsnumber) {
								$numberposts = -1;
							} else {
								$numberposts = $itemsnumber;
							}
							$contenttype = str_replace('posttype_','',$contenttypes);
							$itemlist = get_posts(array('post_type' => $contenttype,'post_status'=>'publish','orderby' => $orderby,'order' => $order,'numberposts' => $numberposts, /*'exclude' => $exclude, 'include' => $include ,'fields'=>'ids'*//*,'lang' => $lang,*/ 'no_found_rows' => true, 'suppress_filters' => false));
							if(strpos($contenttypes,'product') !== false) {
								if ( class_exists( 'WooCommerce' ) ) {
									$currencyposition = get_option( 'woocommerce_currency_pos' );
									$currency = get_woocommerce_currency_symbol();
									if(strpos($currencyposition, 'right') !== false) {
										if($currencyposition == 'right_space') {
											$currency = '&nbsp;'.$currency;
										}
										$currency = '<span class="mr-price-currency" style="order:1">'.$currency.'</span>';
									} else {
										if($currencyposition == 'left_space') {
											$currency = $currency.'&nbsp;';
										}
										$currency = '<span class="mr-price-currency">'.$currency.'</span>';
									}
								}
							}
						} else if ($contenttypes === 'custom_items') {
							$contenttype = 'custom_items';
							$itemlist = array();
							for ($id = 0 ; $id < $itemsnumber; $id++) {
								$customid = $id+1;
								//if($excludeinclude === 0 AND !in_array($customid, $itemselect) OR $excludeinclude === 1 AND in_array($customid, $itemselect) OR $is_admin) {
									$itemlist[$customid] = (object) array('ID' => $customid, /*'post_author' => 1, 'post_date' => 2020-04-18 16:56:51, 'post_date_gmt' => 2020-04-18 16:56:51,*/ 'post_content' => '', 'post_title' => '', 'post_name' => '' /*, 'post_modified' => 2020-04-18 16:56:52, 'post_modified_gmt' => 2020-04-18 16:56:52*/);
								//}
							}
						}
						//set_transient( $widgetid.'_itemlist', $itemlist, 12 * HOUR_IN_SECONDS );
					//}
					//print_r($itemlist);
					if ( !empty( $itemlist ) ) {
							if($is_admin === false) {
								$content .= '<div class="mrdev-widget mr-'.$contenttype.' mr-theme mr-'.strtolower($theme).'theme mr-boxsize '.$widgetclasses.'"><div class="mr-layout mr-'.strtolower($layout).'layout '.(($layoutoptions)?' mr-'.implode(" mr-", $layoutoptions):" ").(($globallayoutoptions)?' mr-'.implode(" mr-", $globallayoutoptions):" ").(($itemoptions)?' mr-'.implode(" mr-", $itemoptions):" ").((!empty($tabs))?' mr-hastabs':" ").((!empty($tabs) && $tabsposition != 'tabstop')?' mr-widget-'.$tabsposition:" ").(($imagestypes)?' mr-'.implode(" mr-", $imagestypes):" ").$autoplayclass.' mr-flex mr-wrap mr-relative mr-top mr-noscroll">';
								if($tabs === 1) { //Items Tabs
									$content .= '<ul class="mr-widget-tabs mr-widget-items mr-flex mr-widget-scroll mr-nobullets">';
									foreach ( $itemlist as $key => $tab) {
										if (strpos($contenttypes, 'taxonomy_') !== false) {
											$tabid = $tab->term_id;
											$tabname = $tab->name;
											$tabslug = $tab->slug;
										} else if (strpos($contenttypes, 'posttype_') !== false || $contenttypes === 'custom_items') {
											$tabid = $tab->ID;
											$tabname = $tab->post_title;
											$tabslug = $tab->post_name;
										}
										if($mrdev_widget_contentoverride == 1 && !empty($titleoverride[$tabid]) || $contenttypes === 'custom_items' && !empty($titleoverride[$tabid])) {
											$tabname = $titleoverride[$tabid];
										}
										/*
										If there is a pinned item, pin also the tab.
										*/
										if(empty($pin)) {
											$pinned = null;
										} else if(in_array($tabid, $pin)) {
											$pinned = ' mr-active';
										} else if(!in_array($tabid, $pin)) {
											$pinned = ' mr-inactive';
										}
										/*
										Get the manual order also for the tabs
										*/
										if (isset($manualordering[$tabid])) {
											$manualorder = '-ms-flex-order: '.$manualordering[$tabid].'; -webkit-order: '.$manualordering[$tabid].'; order: '.$manualordering[$tabid].';';
										} else {
											$manualorder = '-ms-flex-order: 0; -webkit-order: 0; order: 0;';
										}
										$content .= '<li class="itemid-'.$tabid.' '.$tabslug.' mr-widget-tab'.$pinned.'" style="'.$manualorder.'">'.$tabname.'</li>';
									}
									$content .= '</ul>';
								} else if($tabs === 2 && $contenttypes != 'custom_items') { //Items Parent Tabs
									$content .= '<ul class="mr-widget-tabs mr-parentitems mr-flex mr-widget-scroll mr-nobullets">';
									foreach ( $itemlist as $key => $tab) {
										if (strpos($contenttypes, 'taxonomy_') !== false) {
											$tabid = $tab->term_id;
											$tabname = $tab->name;
											$tabslug = $tab->slug;
										} else if (strpos($contenttypes, 'posttype_') !== false) {
											$tabid = $tab->ID;
											$tabname = $tab->post_title;
											$tabslug = $tab->post_name;
										}
										if($mrdev_widget_contentoverride == 1 && !empty($titleoverride[$tabid])) {
											$tabname = $titleoverride[$tabid];
										}
										if (isset($manualordering[$tabid])) {
											$manualorder = '-ms-flex-order: '.$manualordering[$tabid].'; -webkit-order: '.$manualordering[$tabid].'; order: '.$manualordering[$tabid].';';
										} else {
											$manualorder = '-ms-flex-order: 0; -webkit-order: 0; order: 0;';
										}
										$content .= '<li class="parentitemid-'.$tabid.' '.$tabslug.' mr-widget-tab" style="'.$manualorder.'">'.$tabname.'</li>';
									}
									$content .= '</ul>';
								} else if($tabs === 3 && $contenttypes != 'custom_items') { //Categories Tabs
									if($contenttypes == 'posttype_product') {
										$taxonomy = 'product_cat';
									} else {
										$taxonomy = 'category';
									}
									$catlist = get_terms(array('taxonomy' => $taxonomy, 'hide_empty' => true, 'lang' => '', 'hierarchical' => true,'no_found_rows' => true, 'suppress_filter' => false));
									if ( !empty( $catlist ) ) {
										$content .= '<ul class="mr-widget-tabs mr-categories mr-flex mr-widget-scroll mr-nobullets">';
										foreach ( $catlist as $key => $tab) {
											$tabid = $tab->term_id;
											$tabname = $tab->name;
											$tabslug = $tab->slug;
											$content .= '<li class="catid-'.$tabid.' '.$tabslug.' mr-widget-tab">'.$tabname.'</li>';
										}
										$content .= '</ul>';
									}
								} else if($tabs === 4 && $contenttypes != 'custom_items') { //Tags Tabs
									if($contenttypes == 'posttype_product') {
										$taxonomy = 'product_tag';
									} else {
										$taxonomy = 'post_tag';
									}
									$taglist = get_terms(array('taxonomy' => $taxonomy, 'hide_empty' => true, 'lang' => '', 'hierarchical' => true,'no_found_rows' => true, 'suppress_filter' => false));
									if ( !empty( $taglist ) ) {
										$content .= '<ul class="mr-widget-tabs mr-tags mr-flex mr-widget-scroll mr-nobullets">';
										foreach ( $taglist as $key => $tab) {
											$tabid = $tab->term_id;
											$tabname = $tab->name;
											$tabslug = $tab->slug;
											$content .= '<li class="tagid-'.$tabid.' '.$tabslug.' mr-widget-tab">'.$tabname.'</li>';
										}
										$content .= '</ul>';
									}
								}
							}
							$itemcount = 0;
							$pagecount = 1;
							foreach ( $itemlist as $key => $item) {
								/*
								Get all needed item values
								*/
								//print_r($item);
								if($contenttypes === 'custom_items') { //Custom items
									$itemid = $item->ID;
									$itemslug = null;
									if($itemsauthor != 0 && !empty($authoroverride[$itemid])) {
										$authorid = $authoroverride[$itemid];
									} else if($itemsauthor != 0) {
										$authorid = 0;
									}
									$itemparent = 0;
									$parentcheck = 1;
									$itemcats = array();
									$itemtags = array();
									if(!empty($titleoverride[$itemid])) {
										$itemtitle = $titleoverride[$itemid];
									} else {
										$itemtitle = '';
									}
									$showitemspecs = null;
								} else if (strpos($contenttypes, 'taxonomy_') !== false) { //Taxonomies
									$itemid = $item->term_id;
									$itemslug = $item->slug;
									if($mrdev_widget_contentoverride == 1 && !empty($titleoverride[$itemid])) {
										$itemtitle = $titleoverride[$itemid];
									} else {
										$itemtitle = $item->name;
									}
									if($itemsauthor != 0 && $mrdev_widget_contentoverride == 1 && !empty($authoroverride[$itemid])) {
										$authorid = $authoroverride[$itemid];
									} else if($itemsauthor != 0) {
										$authorid = 0;
									}
									$itemparent = $item->parent;
									$itemcats = array($item->parent);
									$itemtags = array();
									if(array_intersect($itemcats,$parentcats) || !$parentcats && !$parenttags && !$parentpages || $itemstaxonomies === 3 || $itemstaxonomies === 6) {
										$parentcheck = 1;
									} else {
										$parentcheck = 0;
									}
									$showitemspecs = null;
								} else if (strpos($contenttypes, 'posttype_') !== false) { //Post types
									$itemid = $item->ID;
									//$itemid = $item;
									$itemslug = $item->post_name;
									//$itemslug = get_post_field('post_name',$itemid);
									if($mrdev_widget_contentoverride == 1 && !empty($titleoverride[$itemid])) {
										$itemtitle = $titleoverride[$itemid];
									} else {
										$itemtitle = $item->post_title;
										//$itemtitle = get_post_field('post_title',$itemid);
									}
									if($itemsauthor != 0 && $mrdev_widget_contentoverride == 1 && !empty($authoroverride[$itemid])) {
										$authorid = $authoroverride[$itemid];
									} else if($itemsauthor != 0) {
										$authorid = $item->post_author;
									}
									$itemparent = $item->post_parent;
									//$itemparent = get_post_field('post_parent',$itemid);
									if($tabs === 3 || !empty($parentcats) || $itemstaxonomies === 1 || $itemstaxonomies === 2 || $itemstaxonomies === 4 || $itemstaxonomies === 5) {
										if($contenttypes == 'posttype_product') {
											$itemcatsterm = 'product_cat';
										} else {
											$itemcatsterm = 'category';
										}
										$itemcats = wp_get_post_terms($itemid,$itemcatsterm,array('fields'=>'ids'));
										if($itemcats && !is_array($itemcats)) {
											$itemcats = array($itemcats);
										} else if(!$itemcats) {
											$itemcats = array();
										}
									} else {
										$itemcats = array();
									}
									if($tabs === 4 || !empty($parenttags) || $itemstaxonomies === 1 || $itemstaxonomies === 3 || $itemstaxonomies === 4 || $itemstaxonomies === 6) {
										if($contenttypes == 'posttype_product') {
											$itemtagsterm = 'product_tag';
										} else {
											$itemtagsterm = 'post_tag';
										}
										$itemtags = wp_get_post_terms($itemid,$itemtagsterm,array('fields'=>'ids'));
										if($itemtags && !is_array($itemtags)) {
											$itemtags = array($itemtags);
										} else if(!$itemtags) {
											$itemtags = array();
										}
									} else {
										$itemtags = array();
									}
									if(array_intersect($itemcats,$parentcats) || array_intersect($itemtags,$parenttags) || array_intersect(array($itemparent),$parentpages) || !$parentcats && !$parenttags && !$parentpages || $itemstaxonomies === 3 || $itemstaxonomies === 6) {
										$parentcheck = 1;
									} else {
										$parentcheck = 0;
									}
									if(strpos($contenttypes,'product') !== false && class_exists( 'WooCommerce' )) {
											$productdata = wc_get_product( $itemid );
											$productprice = $productdata->get_regular_price();
											if(!empty($productprice)) {
												$productsale = $productdata->get_sale_price();
												$showitemspecs = '<div class="mr-specs">';
												$showitemspecs .= '<span class="mr-price">';
													$showitemspecs .= '<span class="mr-price-regular">';
														$showitemspecs .= (!empty($productsale)) ? '<del>':'';
														$showitemspecs .= '<bdi class="mr-flex">'.$currency.'<span class="mr-price-value">'.$productprice.'</span></bdi>';
														$showitemspecs .= (!empty($productsale)) ? '</del>':'';
													$showitemspecs .= '</span>';
													$showitemspecs .= (!empty($productsale)) ? '<span class="mr-price-sale"><bdi><ins class="mr-flex">'.$currency.'<span class="mr-price-value">'.$productsale.'</span></bdi></ins>' : '';
													$showitemspecs .= '</span>';
												$showitemspecs .= '</span>';
												$showitemspecs .= '</div>';
											} else {
												$showitemspecs = null;
											}
									} else {
										$showitemspecs = null;
									}
								}
								if($itemstitlemax != 0) {
									$itemtitle = (strlen($itemtitle) > $itemstitlemax) ? mb_substr($itemtitle,0,absint($itemstitlemax), 'utf-8').'<span class="mr-ellipsis">...</span>' : $itemtitle;
								}
								if(in_array("artcount", $itemoptions) && strpos($contenttypes, 'taxonomy_') !== false) {
									$num_articles = $item->count;
								} else {
									$num_articles = null;
								}
								if($itemdesc != 1 || $itemimage === 8) { // If we don't display description but we still want to get the first image from it we get it here.
									if (strpos($contenttypes, 'taxonomy_') !== false) {
										$itemdescription = $item->description;
									} else if (strpos($contenttypes, 'posttype_') !== false) {
										$itemdescription = $item->post_content;
										//$itemdescription = get_post_field('post_content',$itemid);
									} else if($contenttypes === 'custom_items' && !empty($textoverride[$itemid])) {
										$itemdescription = $textoverride[$itemid];
									} else {
										$itemdescription = null;
									}
								} else {
									$itemdescription = null;
								}
								if($mrdev_widget_contentoverride == 1 && !empty($linkoverride[$itemid]) || $contenttypes === 'custom_items' && !empty($linkoverride[$itemid]) ) {
									$itemurl = $linkoverride[$itemid];
								} else if (strpos($contenttypes, 'taxonomy_') !== false) {
									$itemurl = str_replace("/./","/",get_category_link($itemid));
								} else if (strpos($contenttypes, 'posttype_') !== false) {
									$itemurl = str_replace("/./","/",get_permalink($itemid));
								} else {
									$itemurl = null;
								}
								if($mrdev_widget_contentoverride == 1 && !empty($itemlinktargetoverride[$itemid]) || $contenttypes === 'custom_items' && !empty($itemlinktargetoverride[$itemid]) ) {
									$itemlinktargetrel = 'target="_'.$itemlinktargetoverride[$itemid].'"';
									if($itemlinktargetoverride[$itemid] != "self") {
										$itemlinktargetrel = $itemlinktargetrel.' rel="noopener noreferrer"';
									}
								} else {
									$itemlinktargetrel = 'target="_'.$itemlinktarget.'"';
									if($itemlinktarget != "self") {
										$itemlinktargetrel = $itemlinktargetrel.' rel="noopener noreferrer"';
									}
								}
								if(!empty($itemurl) && strpos($itemurl,'/') == false && strpos($itemurl,'add-to-cart') !== false) {
									if ( class_exists( 'WooCommerce' ) ) {
										$itemurl_components = parse_url(esc_url($itemurl));
										parse_str($itemurl_components['query'], $itemurl_params);
										if(strpos($itemurl_params['add-to-cart'],',') == false) { //Not compatible with multiple products
											$itemlinktargetrel .= ' data-product_id="'.$itemurl_params['add-to-cart'].'"';
										}
									}
								}
								/*
								Check if current item belongs to the selected parents
								*/
								if($parentcheck === 1) {
									/*
									Check if subitems should be excluded
									*/
									if($contenttypes === 'custom_items' || strpos($contenttypes, 'posttype_') !== false || empty($taxonomies_groupexclude) || $excludeinclude != 0 || $excludeinclude === 0 && !in_array('nosubitem', $taxonomies_groupexclude) ||$excludeinclude === 0 && in_array('nosubitem',$taxonomies_groupexclude) && !$itemparent) {
									/*
									Get the manual order value for the current item.
									The default is '0'.
									*/
									if (!empty($manualordering[$itemid])) {
										$manualorder = '-ms-flex-order: '.$manualordering[$itemid].'; -webkit-order: '.$manualordering[$itemid].'; order: '.$manualordering[$itemid].';';
									} else {
										$manualorder = '-ms-flex-order: 0; -webkit-order: 0; order: 0;';
									}
										/*
										Add the content of the current item in the container.
										The layout options are imploded in here has classes.
										*/
									if($is_admin === true) {
											/*
											Check if this item should be on a new page.
											*/
											if($itemcount === 0) {
												/*----- PAGES ORDER -----*/
												echo '<div class="mr-list-container" style="-ms-flex-order:'.(!empty($pageorder[$pagecount]) ? $pageorder[$pagecount] : $pagecount).'; -webkit-order:'.(!empty($pageorder[$pagecount]) ? $pageorder[$pagecount] : $pagecount).'; order:'.(!empty($pageorder[$pagecount]) ? $pageorder[$pagecount] : $pagecount).';">';
												?>
												<div class="mr-widget-page">
													<?php
													if(!isset($mrdev_get_user)) {
														$mrdev_get_user = wp_get_current_user();
													}
													if(!isset($mrdev_get_userrole)) {
														$mrdev_get_userrole = $mrdev_get_user->roles[0];
													}
													if(!isset($mrdev_get_username)) {
														$mrdev_get_username = $mrdev_get_user->user_login;
													}
													$mrdev_widget_content_access = isset($getpluginsettings['mrdeveloper_content']) ? $getpluginsettings['mrdeveloper_content'] : '';
													if(!is_array( $mrdev_widget_content_access ) && $mrdev_widget_content_access == $mrdev_get_username || is_array( $mrdev_widget_content_access ) && in_array( $mrdev_get_username , $mrdev_widget_content_access ) || !is_array( $mrdev_widget_content_access ) && $mrdev_widget_content_access == $mrdev_get_userrole || is_array( $mrdev_widget_content_access ) && in_array( $mrdev_get_userrole , $mrdev_widget_content_access )) {
														$mrdev_widget_content_access = 'Denied';
													} else {
														$mrdev_widget_content_access = 'Allowed';
													}
													?>
													<input <?php if($mrdev_widget_content_access === 'Denied') { echo 'disabled'; } ?> type="number" name="<?php echo esc_attr( $this->get_field_name( 'pageorder' ) ); ?>[<?php echo $pagecount; ?>]" min="1" class="mr-input mr-widget-pageinput" placeholder="<?php echo $pagecount; ?>" <?php if(in_array($pagecount,$pageorder) && !empty($pageorder[$pagecount] )) { echo 'value="'.esc_attr( $pageorder[$pagecount]).'"'; } else { echo 'value="'.$pagecount.'"'; } if(in_array($pagecount,$pageorder) && $pageorder[$pagecount] !== $pagecount && $pageorder[$pagecount] !== 0) { echo 'style="border-right-width: 3px;"'; }  ?> title="Reorder page by number. Leave empty to return to its original number. Repeated values are not allowed for pages.">
													<hr>
												</div>
												<?php
											}
											echo '<div class="mr-widget-item" style="'.$manualorder.'">';
												if(file_exists(trailingslashit(plugin_dir_path( __DIR__ )).'mrdev-framework_wp/settings/widget/orderpin.php')) {
													include trailingslashit(plugin_dir_path( __DIR__ )).'mrdev-framework_wp/settings/widget/orderpin.php';
												}
												?>
												<label <?php if(!file_exists(trailingslashit(plugin_dir_path( __DIR__ )).'mrdev-framework_wp/settings/widget/orderpin.php')) { echo 'style="margin-left: 48px;"'; } ?>><input <?php if($mrdev_widget_content_access === 'Denied') { echo 'disabled'; } ?> type="checkbox" class="mr-checkbox" name="<?php echo esc_attr( $this->get_field_name( 'itemselect' ) ); ?>[]" value="<?php echo $itemid; ?>" title="Exclude or include this item, depending of the selected option on the top." <?php checked( (in_array( $itemid, $itemselect ) ) ? $itemid : '', $itemid ); ?>/>
												<?php
													if($mrdev_widget_contentoverride == 1 && file_exists(trailingslashit(plugin_dir_path( __DIR__ )).'mrdev-framework_wp/settings/widget/customitems.php') || $contenttypes === 'custom_items' && file_exists(trailingslashit(plugin_dir_path( __DIR__ )).'mrdev-framework_wp/settings/widget/customitems.php')) {
														include trailingslashit(plugin_dir_path( __DIR__ )).'mrdev-framework_wp/settings/widget/customitems.php';
													} else {
														if (strpos($contenttypes, 'taxonomy_') !== false) { echo $item->name; } else if (strpos($contenttypes, 'posttype_') !== false) { echo $item->post_title.'</label>';}
													}
											echo "</div>";
											$itemcount = ($itemcount + 1);
											/*
											If the option 'only show subitems of active' is enabled and this item is a subcategory, it should not close the page yet.
											*/
											if(in_array( "subitemactive", $globallayoutoptions ) && $itemparent > 0) {
											} else {
												if($itemcount == $perpage) {
													echo '</div>';
													$itemcount = 0;
													$pagecount = ($pagecount + 1);
												}
											}
									} else {
											if($excludeinclude != 0 || $excludeinclude === 0 && in_array('samelink', $groupexclude) && !in_array('differentlink', $groupexclude) && $itemurl != $currentLink || $excludeinclude === 0 && in_array('differentlink', $groupexclude) && !in_array('samelink', $groupexclude) && $itemurl === $currentLink || empty($groupexclude) || !in_array('samelink', $groupexclude) && !in_array('differentlink', $groupexclude) || $excludeinclude === 1) {
												/*
												Check if current item should be excluded/included.
												*/
												if($excludeinclude === 0 AND !in_array($itemid, $itemselect) OR $excludeinclude === 1 AND in_array($itemid, $itemselect) OR $is_admin === true) {
													/*
													Recheck if it's to exclude items with no posts.
													*/
													if($contenttypes === 'custom_items' || strpos($contenttypes, 'posttype_') !== false || empty($taxonomies_groupexclude) || !in_array('noarticles',$taxonomies_groupexclude) || $num_articles === null || in_array('noarticles',$taxonomies_groupexclude) && is_numeric($num_articles) && $num_articles > 0) {
														/*
														Check if sticky items should be included (exclude not sticky)
														*/
														if($contenttypes === 'custom_items' || $excludeinclude === 1 || is_sticky($itemid) && in_array('notfeatured', $posttypes_groupexclude) || !in_array('notfeatured', $posttypes_groupexclude) || empty($posttypes_groupexclude) || strpos($contenttypes, 'taxonomy_') !== false ) {
														/*
														Check if sticky items should be excluded
														*/
														if($contenttypes === 'custom_items' || $excludeinclude === 1 || !is_sticky($itemid) && in_array('featured', $posttypes_groupexclude) || !in_array('featured', $posttypes_groupexclude) || empty($posttypes_groupexclude) || strpos($contenttypes, 'taxonomy_') !== false ) {
															/*
															Image starts here
															*/
															if($itemimage === 9) {
																$showimage = null;
																$getimg = null;
															} else if($mrdev_widget_contentoverride == 1 && !empty($imageoverride[$itemid]) || $contenttypes === 'custom_items' && !empty($imageoverride[$itemid])) {
																$getimg = $imageoverride[$itemid];
															} else {
																$showimage = null;
																$getimg = null;
																if ($itemimage === 8) { //Description first image
																	$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $itemdescription, $matches);
																	if(!empty($output) && !empty($matches)) {
																		$getimg = $matches[1][0];
																	}
																}
																if($contenttypes != 'custom_items') {
																	if ($itemimage === 1) { //Item image
																		if (strpos($contenttypes, 'posttype_') !== false) {
																			$getimg = get_post_thumbnail_id($itemid);
																		} else {
																			if($itemdescription) {
																				$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $itemdescription, $matches);
																				if(!empty($output) && !empty($matches)) {
																					$getimg = $matches[1][0];
																				}
																			}
																		}
																	} else if ($itemimage === 2 || $itemimage === 5) { //Post images
																		if($itemimage === 2) { //Latest sticky post
																			$sticky = get_option( 'sticky_posts' );
																			if ( !empty($sticky) ) {
																				if (strpos($contenttypes, 'taxonomy_') !== false) {
																					$posts = get_posts(array('posts_per_page' => 1,'orderby' => 'date','order'=>'DESC','category__in' => $itemid,'post_status' => 'publish','post__in' => $sticky,'ignore_sticky_posts' => 1, 'no_found_rows' => true, 'suppress_filters' => false));
																				} else {
																					$posts = get_posts(array('posts_per_page' => 1,'orderby' => 'date','order'=>'DESC','category__in' => $itemcats[0],'post_status' => 'publish','post__in' => $sticky,'ignore_sticky_posts' => 1, 'no_found_rows' => true, 'suppress_filters' => false));
																				}
																			} else {
																				unset($posts);
																			}
																		} else if($itemimage === 5) { //Latest post
																			if (strpos($contenttypes, 'taxonomy_') !== false) {
																				$posts = get_posts(array('posts_per_page' => 1,'orderby' => 'date','order'=>'DESC','category__in' => $itemid,'post_status' => 'publish', 'no_found_rows' => true, 'suppress_filters' => false));
																			} else {
																				$posts = get_posts(array('posts_per_page' => 1,'orderby' => 'date','order'=>'DESC','category__in' => $itemcats[0],'post_status' => 'publish', 'no_found_rows' => true, 'suppress_filters' => false));
																			}
																		} else {
																			unset($posts);
																		}
																		if(isset($posts)) {
																			$getimg = get_post_thumbnail_id($posts[0]->ID);
																		} else {
																			unset($getimg);
																		}
																	}
																}
															}
															if(empty($getimg) && !empty($fallbackimage)) {
																$getimg = $fallbackimage;
															}
															if(!empty($getimg)) {
																if(is_numeric($getimg)) {
																	$imgid = $getimg;
																} else {
																	$imgid = attachment_url_to_postid($getimg);
																}
																if($imgid != 0) {
																	if(wp_get_attachment_image_src($imgid, 'large')[0]) {
																		$getimgLarge = wp_get_attachment_image_src($imgid, 'large')[0];
																	} else {
																		$getimgLarge = wp_get_attachment_image_src($imgid, 'full')[0];
																	}
																} else {
																	$getimgLarge = $getimg;
																}
																//$getimgMidLarge = wp_get_attachment_image_src($imgid, 'medium_large')[0];
																if($imagestypes && in_array('thumbnail',$imagestypes)) {
																	if($imgid != 0) { //If it's local image
																		$imgdata = wp_prepare_attachment_for_js($imgid);
																		$imgcaption = $imgdata['caption'];
																		if(!empty($imgcaption)) {
																			$imgcaption = '<figcaption><small>'.$imgcaption.'</small></figcaption>';
																		} else {
																			$imgcaption = null;
																		}
																		$imgtitle = $imgdata['title'];
																		if(!empty($imgtitle)) {
																			if (strpos($getimgLarge, $imgtitle) === false) {
																				$imgtitle = ' title="'.$imgtitle.'" ';
																			} else {
																				$imgtitle = null;
																			}
																		} else {
																			$imgtitle = null;
																		}
																		if(!empty($imagemaxwidth)) {
																			$imgwidth = ' width="'.$imagemaxwidth.'" ';
																			$imgsizes = ' sizes="(min-width: '.$imagemaxwidth.'px) '.$imagemaxwidth.'px, 100vw"';
																		} else {
																			$imgwidth = null;
																			$imggetwidth = $imgdata['width'];
																			if($imggetwidth) {
																				$imgwidth = ' width="'.$imggetwidth.'" ';
																			}
																			$imgsizes = ' sizes="100vw" ';
																		}
																		if(!empty($imagemaxheight)) {
																			$imgheight = ' height="'.$imagemaxheight.'" ';
																		} else {
																			$imgheight = null;
																			$imggetheight = $imgdata['height'];
																			if($imggetheight) {
																				$imgheight = ' height="'.$imggetheight.'" ';
																			}
																		}
																	} else { //If it's an external image
																		$imgdata = null;
																		$imgwidth = null;
																		$imgheight = null;
																		$imgsizes = null;
																		$imgtitle = null;
																		$imgcaption = null;
																	}
																	if(in_array('background',$imagestypes)) {
																		$showimage = "><figure class='mr-image' style='background-image: url(".esc_url($getimgLarge).");".$addimagemaxwidth.$addimagemaxheight."'".$imgtitle.">".$imgcaption."</figure";
																	} else {
																		if($imgid != 0) {
																			$imgsrcset = wp_get_attachment_image_srcset($imgid);
																			if(empty($imgsrcset) || strpos($imgsrcset,'.gif') !== false) {
																				$imgsrcset = null;
																			} else {
																				$imgsrcset = ' srcset="'.$imgsrcset.'" ';
																			}
																			$imgalt = $imgdata['alt'];
																			if(empty($imgalt)) {
																				$imgalt = null;
																			} else {
																				$imgalt = ' alt="'.$imgalt.'" ';
																			}
																		} else {
																			$imgsrcset = null;
																			$imgalt = null;
																		}
																		$showimage = "><figure class='mr-image' style='".$addimagemaxwidth."'><img loading='lazy' src='".esc_url($getimgLarge)."' ".$imgsrcset.$imgwidth.$imgheight.$imgsizes.$imgtitle.$imgalt." style='".$addimagemaxheight."'/>".$imgcaption."</figure";
																	}
																} else {
																	$showimage = "><figure class='mr-image' style='background-image: url(".esc_url($getimgLarge).");'></figure";
																}
															}
															/*
															Title starts here
															*/
															$itemtitleclasses = 'mr-title';
															if($itemstitle === 1)  { //No title
																$showitemtitle = ''.((in_array("artcount", $itemoptions) && is_numeric($num_articles))?'<'.$titletag.' class="'.$itemtitleclasses.'">('.$num_articles.')</'.$titletag.'>':"");
															} else if($itemurl === null || $itemstitle === 2) { //Item title
																$showitemtitle = '<'.$titletag.' class="'.$itemtitleclasses.'">'.$itemtitle.((in_array("artcount", $itemoptions) && is_numeric($num_articles))?' <small>('.$num_articles.')</small>':"").'</'.$titletag.'>';
															} else { //Linked item title
																if(strpos($itemlinktargetrel,'data-product_id') !== false) {
																	$itemtitleclasses .= ' add_to_cart_button ajax_add_to_cart';
																}
																$showitemtitle = '<'.$titletag.' class="'.$itemtitleclasses.'">'.'<a href="'.esc_url($itemurl).'" '.$itemlinktargetrel.'>'.$itemtitle.((in_array("artcount", $itemoptions) && is_numeric($num_articles))?' <small>('.$num_articles.')</small>':"").'</a>'.'</'.$titletag.'>';
															}
															/*
															Date starts here
															*/
															if($itemsdate != 0)  {
																if(!empty($dateoverride[$itemid])) {
																	$showitemdate = '<time class="mr-date"><small>';
																	$showitemdate .= $itemdatelabel.$dateoverride[$itemid];
																	$showitemdate .= '</small></time>';
																} else if(strpos($contenttypes, 'posttype_') !== false) {
																	if($itemsdate === 1)  { //Published date
																		$showitemdate = '<time class="mr-date"><small>';
																		$showitemdate .= $itemdatelabel.$item->post_date;
																		$showitemdate .= '</small></time>';
																	} else if($itemsdate === 2)  { //Modified date
																		$showitemdate = '<time class="mr-date"><small>';
																		$showitemdate .= $itemdatelabel.$item->post_modified;
																		$showitemdate .= '</small></time>';
																	} else {
																		$showitemdate = '';
																	}
																} else {
																	$showitemdate = '';
																}
															} else { //No date
																$showitemdate = '';
															}
															/*
															Author starts here
															*/
															if($itemsauthor != 0 && $authorid != 0)  {
																$itemauthor = get_the_author_meta('display_name',$authorid);
																$authorurl = get_author_posts_url($authorid);
																$authorpic = get_avatar_url($authorid,array('size' => 50));
																$showitemauthor = '<span class="mr-author"><small>';
																if($itemsauthor === 1)  { //Linked item author with profile picture
																	$showitemauthor .= $itemauthorlabel.'<a class="mr-authorurl" href="'.$authorurl.'" title="'.$itemauthor.'">'.'<img class="mr-authorpic" src="'.$authorpic.'" alt="'.$itemauthor.'"> '.$itemauthor.'</a>';
																} else if($itemsauthor === 2)  { //Linked item author
																	$showitemauthor .= $itemauthorlabel.'<a class="mr-authorurl" href="'.$authorurl.'" title="'.$itemauthor.'">'.$itemauthor.'</a>';
																} else if($itemsauthor === 3)  { //Item author with profile picture
																	$showitemauthor .= $itemauthorlabel.'<img class="mr-authorpic" src="'.$authorpic.'" title="'.$itemauthor.'" alt="'.$itemauthor.'"> '.$itemauthor;
																} else if($itemsauthor === 4) { //Item author
																	$showitemauthor .= $itemauthorlabel.$itemauthor;
																}
																$showitemauthor .= '</small></span>';
															} else { //No author
																$showitemauthor = '';
															}
															/*
															Taxonomies start here
															*/
															if($itemstaxonomies != 0)  {
																$showitemtaxonomies = '<span class="mr-taxonomies"><small>';
																if($itemstaxonomies === 1 && !empty($itemtags) || $itemstaxonomies === 3 && !empty($itemtags)) { //Linked tags
																	$showitemtaxonomies .= $itemtaxonomieslabel;
																	foreach($itemtags as $i => $itemtag) {
																		if ($i > 0) {
																			$showitemtaxonomies .= ', ';
																		}
																		$showitemtaxonomies .= '<a class="tagid-'.$itemtag.' mr-taxonomy mr-taxonomylink" href="'.get_term_link($itemtag, $itemtagsterm).'">'.get_term($itemtag,$itemtagsterm)->name.'</a>';
																	}
																	if($itemstaxonomies === 1 && !empty($itemcats)) {
																		$showitemtaxonomies .= ' & ';
																	}
																}
																if($itemstaxonomies === 1 && !empty($itemcats) || $itemstaxonomies === 2 && !empty($itemcats)) { //Linked categories
																	if($itemstaxonomies === 2 || empty($itemtags)) {
																		$showitemtaxonomies .= $itemtaxonomieslabel;
																	}
																	foreach($itemcats as $i => $itemcat) {
																		if ($i > 0) {
																			$showitemtaxonomies .= ', ';
																		}
																		$showitemtaxonomies .= '<a class="catid-'.$itemcat.' mr-taxonomy mr-taxonomylink" href="'.get_term_link($itemcat, $itemcatsterm).'">'.get_term($itemcat,$itemcatsterm)->name.'</a>';
																	}
																}
																if($itemstaxonomies === 4 && !empty($itemtags) || $itemstaxonomies === 6 && !empty($itemtags)) { //Tags
																	$showitemtaxonomies .= $itemtaxonomieslabel;
																	foreach($itemtags as $i => $itemtag) {
																		if ($i > 0) {
																			$showitemtaxonomies .= ', ';
																		}
																		$showitemtaxonomies .= '<span class="tagid-'.$itemtag.' mr-taxonomy">'.get_term($itemtag,$itemtagsterm)->name.'</a>';
																	}
																	if($itemstaxonomies === 4 && !empty($itemcats)) {
																		$showitemtaxonomies .= ' & ';
																	}
																}
																if($itemstaxonomies === 4 && !empty($itemcats) || $itemstaxonomies === 5 && !empty($itemcats)) { //Categories
																	if($itemstaxonomies === 5 || empty($itemtags)) {
																		$showitemtaxonomies .= $itemtaxonomieslabel;
																	}
																	foreach($itemcats as $i => $itemcat) {
																		if ($i > 0) {
																			$showitemtaxonomies .= ', ';
																		}
																		$showitemtaxonomies .= '<span class="catid-'.$itemcat.' mr-taxonomy">'.get_term($itemcat,$itemcatsterm)->name.'</span>';
																	}
																}
																$showitemtaxonomies .= '</small></span>';
															} else { //No taxonomies
																$showitemtaxonomies = '';
															}
															/*
															Get all items' meta
															*/
															if($showitemauthor === '' && $showitemdate === '') {
																$showitemmeta = '';
															} else {
																$showitemmeta = '<div class="mr-meta mr-flex">'.$showitemdate.$showitemauthor.$showitemtaxonomies.'</div>';
															}
															/*
															Description starts here
															*/
															if($itemdesc === 1) { //No description
																$showitemdesc = null;
															} else { //With description
																if($mrdev_widget_contentoverride == 1 && !empty($textoverride[$itemid]) || $contenttypes === 'custom_items' && !empty($textoverride[$itemid])) { //Override description
																	$itemdescription = $textoverride[$itemid];
																} else if($itemdesc === 4) { //Item excerpt
																	if (strpos($contenttypes, 'posttype_') !== false && has_excerpt($itemid)) {
																		$itemdescription = get_the_excerpt($itemid);
																	} else {
																		$itemdescription = strstr($itemdescription, '<!--more-->', true);
																	}
																} else if($itemdesc === 2) { //Item intro text
																	$itemdescription = strstr($itemdescription, '<!--more-->', true);
																} else if($itemdesc === 3) { //Item full text
																	if (strpos($itemdescription, '<!--more-->') !== false) {
																		$itemdescription = explode('<!--more-->', $itemdescription)[1];
																	}
																} else { //Item description
																	if (strpos($contenttypes, 'posttype_') !== false && has_excerpt($itemid)) {
																		$itemexcerpt = get_the_excerpt($itemid);
																	} else {
																		$itemexcerpt = strstr($itemdescription, '<!--more-->', true);
																	}
																	if(!$itemexcerpt) {
																		$itemdescription = explode('<!--more-->', $itemdescription)[0];
																	} else {
																		$itemdescription = $itemexcerpt;
																	}
																}
																if(!empty($itemdescmax)) {
																	$itemdescription = strip_tags($itemdescription);
																	$itemdescription = (strlen($itemdescription) > $itemdescmax) ? mb_substr($itemdescription,0,$itemdescmax, 'utf-8').'<span class="mr-ellipsis">...</span>' : $itemdescription;
																}
																$showitemdesc = '<div class="mr-desc">'.do_shortcode($itemdescription).'</div>';
															}
															/*
															Bottom link starts here
															*/
															if($itemurl === null || $itemlink === 1) { //No bottom link
																$bottomlinktext="";
															} else { //Item link
																if($bottomlink === "") {
																	$bottomlink = "Know more...";
																}
																if(strpos($itemlinktargetrel,'data-product_id') !== false) {
																	$bottomlinkclasses .= ' add_to_cart_button ajax_add_to_cart';
																}
																$bottomlinktext = '<div class="mr-link"><a class="'.$bottomlinkclasses.'" href="'.esc_url($itemurl).'" '.$itemlinktargetrel.' title="'. strip_tags($itemtitle) .'">'.((!empty($bottomlinkoverride) && !empty($bottomlinkoverride[$itemid]))?$bottomlinkoverride[$itemid]:$bottomlink).'</a></div>';
															}
															/*
															If there is a pinned item, check if it is the current item.
															*/
															if(empty($pin)) {
																$pinned = null;
															} else if(in_array($itemid, $pin)) {
																$pinned = ' mr-active';
															} else if(!in_array($itemid, $pin)) {
																$pinned = ' mr-inactive';
															}
															/*
															Check front for active category/link and adds a class if it's the current category/link.
															*/
															if(is_array($currentitem) && in_array($itemid, $currentitem) || $itemurl === $currentLink) {
																$mrcurrent = 'mr-current';
															} else if($currentitem != '' && $currentitem === $itemid) {
																$mrcurrent = 'mr-current';
															} else {
																$mrcurrent = null;
															}
															$mrclasses = '';
															/*
															Add classes for subitems
															*/
															if($itemparent === 0) {
																$mrclasses .= '';
															} else {
																$mrclasses .= 'mr-subitem parentitemid-'.$itemparent;
																/*
																If the option 'only show subitems of active' is enabled and this item is a subitem, it should be initially hidden.
																*/
																if(in_array( "subitemactive", $globallayoutoptions ) || in_array( "subitemactive", $layoutoptions )) {
																	$mrclasses .= ' mr-hide';
																}
															}
															/*
															Add classes for categories
															*/
															if(!empty($itemcats)) {
																if (strpos($contenttypes, 'taxonomy_') !== false) {
																	$mrclasses .= ' catid-'.$item->term_id;
																} else if (strpos($contenttypes, 'posttype_') !== false) {
																	$mrclasses .= ' catid-'.implode(" catid-",$itemcats);
																}
															} else if(empty($itemcats)) {
																$mrclasses .= '';
															}
															/*
															Add classes for tags
															*/
															if(!empty($itemtags)) {
																$mrclasses .= ' tagid-'.implode(" tagid-",$itemtags);
															} else if(empty($itemtags)) {
																$mrclasses .= '';
															}
															/*
															Check if this item should be on a new page.
															*/
															if($itemcount === 0) {
																if(in_array($pagecount,$pageorder) && $pageorder[$pagecount] != $pagecount && $pageorder[$pagecount] != 0) {
																	$pagenumber = $pageorder[$pagecount];
																} else {
																	$pagenumber = $pagecount;
																}
																$content .= '<ul class="pageid-'.$pagecount.' mr-'.$perline.'perline mr-widget-page'.$pagenumber.' mr-widget-pages mrwidget-'.$perpage.'perpage mr-nobullets mr-'.$pagetransition.''.($pagenumber == 1 ? " mr-active" : " mr-inactive").'" style="-ms-flex-order: '.$pagenumber.'; -webkit-order: '.$pagenumber.'; order: '.$pagenumber.';">';
																if($pagenumber != 1 && in_array(1,$technical)) {
																	$content .= '<noscript class="mr-noscript">';
																}
															}
															$content .= '<li class="itemid-'.$itemid.' '.$itemslug.' '.$mrclasses.' mr-widget-item '.$mrcurrent.$pinned.'" '.((in_array("url", $itemoptions))?'url='.esc_url($itemurl):"").' style="'.$manualorder.'"><div class="mr-widget-item-container"'.$showimage.'>'.$showitemtitle.'<div class="mr-content">'.$showitemmeta.$showitemdesc.$showitemspecs.$bottomlinktext.'</div></div></li>';
															$itemcount = ($itemcount + 1);
															/*
															If the option 'only show subitems of active' is enabled and this item is a subcategory, it should not close the page yet.
															*/
															if(in_array( "subitemactive", $globallayoutoptions ) && $itemparent > 0) {
															} else {
																if($itemcount == $perpage) {
																	if($pagenumber != 1 && in_array(1,$technical)) {
																		$content .= '</noscript>';
																	}
																	$content .= '</ul>';
																	$itemcount = 0;
																	$pagecount = ($pagecount + 1);
																}
															}
														}
													}
												}
											}
										}
									}
								}
								}
							}
							/*
							Doublecheck if the last page was closed in case the last item was hidden.
							*/
							if($itemcount != 0) {
								if($is_admin === true) {
									echo '</div><hr>';
								} else {
									if($pagecount != 1 && in_array(1,$technical)) {
										$content .= '</noscript>';
									}
									$content .= '</ul>';
								}
								$itemcount = 0;
								$pagecount = ($pagecount + 1);
							}
							$pagecount = ($pagecount - 1);
							if($is_admin === false) {
								if($pagecount > 1) {
									$content .= '<div class="mr-pagination '.((in_array(5, $pagetoggles))?'mr-keyboard':"").'">';
									if( in_array( 0, $pagetoggles ) || empty($pagetoggles) && $autoplay === 0) {
										$content .= '<button class="mr-widget-arrows mr-widget-prev"><span><</span></button>';
									}
									if( in_array( 0, $pagetoggles ) || empty($pagetoggles) && $autoplay === 0) {
										$content .= '<button class="mr-widget-arrows mr-widget-next"><span>></span></button>';
									}
									$hideelement = null;
									if( empty( $pagetoggles ) || !in_array( 1, $pagetoggles )) {
										$hideelement = 'mr-hide';
									}
									$content .= '<select class="mr-widget-pageselect '.$hideelement.'" title="/'.$pagecount.'">';
									$pageselect = 0;
									while ($pageselect++ < $pagecount) {
										$content .= '<option value="'.$pageselect.'">'.$pageselect.'</option>';
									}
									$content .= '</select>';
									if( in_array( 2, $pagetoggles )) {
										$content .= '<div class="mr-widget-radios">';
										$pageselect = 0;
										while ($pageselect++ < $pagecount) {
											$content .= '<input name="mr-widget-radio" title="'.$pageselect.'/'.$pagecount.'" class="mr-widget-radio" type="radio" value="'.$pageselect.'"'.(($pageselect==1)?' checked="checked" ':'').'>';
										}
										$content .= '</div>';
									}
									if( in_array( 3, $pagetoggles ) || in_array( 4, $pagetoggles )) {
										$content .=  '<button class="'.((in_array(3, $pagetoggles))?'mr-widget-below':"").' '.((in_array(4, $pagetoggles))?'mr-widget-scroll':"").'"><span>+</span></button>';
									}
									$content .= '</div>';
								}
								$content .= '</div></div>';
							}
					}
				
?>