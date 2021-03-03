<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH . 'libraries/REST_Controller.php');

class Articles extends REST_Controller {
    function __construct() {
        parent::__construct();
        $this->load->library('ion_auth');
        $this->load->helper('app');
        $this->load->helper('httpconst');
        $this->load->helper('authorization');
        $this->load->helper('master');
        $this->load->model('commonapi_model', 'commonapi');
        $this->load->model('Api_client_entity_articles_model', 'client_entity_articles_model');
        $this->load->model('Test_Api_client_entity_articles_model', 'test_client_entity_articles_model');
        $this->load->model('api_entity_model', 'entity_model');
    }
    
    function list_post(){
        $resposeResult = [];
        $resposeDataMsg = "";
        $postData = $this->post();
        $userId = $this->post('user_id');
        $clientId = $this->post('client_id');
        $entity_id = $this->post('entity_id');
        $entity_type = $this->post('entity_type');
        $start_date = $this->post('start_date');
        $end_date = $this->post('end_date');
        $limit = $this->post('limit');
        $offset = $this->post('offset');
       
        $entityTypes = get_entity_type_list(true,true);
        if (empty($postData)) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Please provide valid paramters"; 
        }elseif (empty($clientId) || !is_numeric($clientId)) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Not valid client id"; 
        }elseif (empty($userId) || !is_numeric($userId)) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Not valid user id"; 
        }
        elseif (empty($entity_id) || !is_numeric($entity_id)) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Not valid entity id"; 
        }elseif (empty($start_date) || empty($end_date)) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Start date or end date should not blank"; 
        }elseif ( ( !empty(trim($start_date)) && !empty(trim($end_date)) ) && (strtotime($end_date) < strtotime($start_date)) ) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Not valid date range"; 
        }elseif (empty($entity_type)) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Entity type not found"; 
        } elseif (!empty($entity_type) && !in_array($entity_type,$entityTypes)) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Not valid entity type"; 
        } else {      
           
           $resposeResult = $this->client_entity_articles_model->getUsersClientsEntityArticles($userId,$clientId,$entity_id,$entity_type,$start_date,$end_date,$limit,$offset);
           if(!empty($resposeResult['data']))
            {
//                array_walk($resposeResult['data'], function (&$item) {
//                       $item['publish_date']=date('Y-m-d',strtotime($item['publish_datetime']));
//                    });
                $imagearchiveurl = get_archive_image_path();    
               foreach($resposeResult['data'] as $key =>$itm) {
                    $resposeResult['data'][$key]['publish_date']=date('Y-m-d',strtotime($itm['publish_datetime']));
                    $created_on =strtotime(date('Y-m-d',strtotime($itm['created_on'])));
                    $isExisted = false;
                    foreach($imagearchiveurl as $imgarc) {
                         if( $created_on >= strtotime($imgarc['start_date']) && $created_on <= strtotime($imgarc['end_date']) ) {
                             $isExisted = true;
                         }else{
                             continue;
                         }
                    }
                     if( $isExisted ) {
                         $resposeResult['data'][$key]['imagebaseurl']=image_baseurl($itm);
                     } else {
                         $resposeResult['data'][$key]['imagebaseurl']=get_article_image_path(true);
                     }
                }  
            }

           $row['client_detail'] = $this->commonapi->getByWhere(base_model::$tbl_Clients, 'id', $clientId,'id,client_name,start_date,end_date,contact_person,address,address2,account_leader,department_id,contact_no,contact_email,client_logo,client_main_category,group_name,have_summary,is_active,allow_tag,hide_product_logo,hide_client_logo,has_mail_content,generate_rss_feed,dossier_logo,hide_dossier_logo,show_pdf_headline,show_word_headline,online_regional,show_sqcm_word,show_sqcm_pdf,show_sel_pub_report,hide_metadata_details');

            if ( isset($row['client_detail'][0]) && !empty($row['client_detail'][0])) {
                 $resposeResult['client_detail'] = $row['client_detail'][0];
            }
           
           $resposeResult['imagebaseurl']=get_article_image_path(true);
           $resposeResult = stripslashesArray($resposeResult);
           $resposeDataMsg = "success";
           $responseCode = HTTP_CONST::HTTP_OK;         
        }
        $resposeData = response_format($responseCode,$resposeDataMsg,$resposeResult,$postData);
        $this->response($resposeData,200);
    }
    
	    
    function similar_article_list_post(){
        $resposeResult = [];
        $resposeDataMsg = "";
        $postData = $this->post();
        $userId = $this->post('user_id');
        $clientId = $this->post('client_id');
        $entity_id = $this->post('entity_id');
        $entity_type = $this->post('entity_type');
        $start_date = $this->post('start_date');
        $end_date = $this->post('end_date');
        $limit = $this->post('limit');
        $offset = $this->post('offset');
        $edition_ids = $this->post('edition_ids');
        $publication_ids = $this->post('publication_ids');
       
        $entityTypes = get_entity_type_list(true,true);
        if (empty($postData)) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Please provide valid paramters"; 
        }elseif (empty($clientId) || !is_numeric($clientId)) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Not valid client id"; 
        }elseif (empty($userId) || !is_numeric($userId)) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Not valid user id"; 
        }
        elseif (empty($entity_id) || !is_numeric($entity_id)) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Not valid entity id"; 
        }elseif (empty($start_date) || empty($end_date)) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Start date or end date should not blank"; 
        }elseif ( ( !empty(trim($start_date)) && !empty(trim($end_date)) ) && (strtotime($end_date) < strtotime($start_date)) ) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Not valid date range"; 
        }elseif (empty($entity_type)) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Entity type not found"; 
        } elseif (!empty($entity_type) && !in_array($entity_type,$entityTypes)) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Not valid entity type"; 
        } else {      
           $publication_type_ids = $this->post('publication_type_ids');
           $resposeResult = $this->client_entity_articles_model->getUsersClientsEntitySimilarArticles($userId,$clientId,$entity_id,$entity_type,$start_date,$end_date,$limit,$offset,$edition_ids,$publication_ids,$publication_type_ids);
           if(!empty($resposeResult['data']))
            {
//                array_walk($resposeResult['data'], function (&$item) {
//                       $item['publish_date']=date('Y-m-d',strtotime($item['publish_datetime']));
//                    });
                $imagearchiveurl = get_archive_image_path();    
               foreach($resposeResult['data'] as $key =>$itm) {
				    $currentObjData = $resposeResult['data'][$key];
                    $resposeResult['data'][$key]['publish_date']=date('Y-m-d',strtotime($itm['publish_datetime']));
                    $created_on =strtotime(date('Y-m-d',strtotime($itm['created_on'])));
                    $isExisted = false;
                    foreach($imagearchiveurl as $imgarc) {
                         if( $created_on >= strtotime($imgarc['start_date']) && $created_on <= strtotime($imgarc['end_date']) ) {
                             $isExisted = true;
                         }else{
                             continue;
                         }
                    }
                     if( $isExisted ) {
                         $resposeResult['data'][$key]['imagebaseurl']=image_baseurl($itm);
                     } else {
                         $resposeResult['data'][$key]['imagebaseurl']=get_article_image_path(true);
                     }
					 if(!empty($resposeResult['data'][$key]['merge_unmerge_key']))
					 {
						$groupedData = array_group_by($resposeResult['similar_articles_data'], 'merge_unmerge_key');
						$resposeResult['data'][$key]['similar_articles'] = isset($groupedData[$resposeResult['data'][$key]['merge_unmerge_key']])?$groupedData[$resposeResult['data'][$key]['merge_unmerge_key']]:array();
						if(!empty($resposeResult['data'][$key]['similar_articles']))
						{
							$resposeResult['data'][$key]['similar_articles'] = array_merge(array('0'=>$currentObjData), $resposeResult['data'][$key]['similar_articles']);
						}
					 }
					 else
					 {
						 $resposeResult['data'][$key]['similar_articles'] = array();
					 }
                }  
            }

           $row['client_detail'] = $this->commonapi->getByWhere(base_model::$tbl_Clients, 'id', $clientId,'id,client_name,start_date,end_date,contact_person,address,address2,account_leader,department_id,contact_no,contact_email,client_logo,client_main_category,group_name,have_summary,is_active,allow_tag,hide_product_logo,hide_client_logo,has_mail_content,generate_rss_feed,dossier_logo,hide_dossier_logo,show_pdf_headline,show_word_headline,online_regional,show_sqcm_word,show_sqcm_pdf,show_sel_pub_report,hide_metadata_details');

            if ( isset($row['client_detail'][0]) && !empty($row['client_detail'][0])) {
                 $resposeResult['client_detail'] = $row['client_detail'][0];
            }
           
           $resposeResult['imagebaseurl']=get_article_image_path(true);
           $resposeResult = stripslashesArray($resposeResult);
           $resposeDataMsg = "success";
           $responseCode = HTTP_CONST::HTTP_OK;         
        }
        $resposeData = response_format($responseCode,$resposeDataMsg,$resposeResult,$postData);
        $this->response($resposeData,200);
    }
    

    function test_similar_article_list_post(){
        $resposeResult = [];
        $resposeDataMsg = "";
        $postData = $this->post();
        $userId = $this->post('user_id');
        $clientId = $this->post('client_id');
        $entity_id = $this->post('entity_id');
        $entity_type = $this->post('entity_type');
        $start_date = $this->post('start_date');
        $end_date = $this->post('end_date');
        $limit = $this->post('limit');
        $offset = $this->post('offset');
       
        $entityTypes = get_entity_type_list(true,true);
        if (empty($postData)) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Please provide valid paramters"; 
        }elseif (empty($clientId) || !is_numeric($clientId)) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Not valid client id"; 
        }elseif (empty($userId) || !is_numeric($userId)) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Not valid user id"; 
        }
        elseif (empty($entity_id) || !is_numeric($entity_id)) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Not valid entity id"; 
        }elseif (empty($start_date) || empty($end_date)) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Start date or end date should not blank"; 
        }elseif ( ( !empty(trim($start_date)) && !empty(trim($end_date)) ) && (strtotime($end_date) < strtotime($start_date)) ) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Not valid date range"; 
        }elseif (empty($entity_type)) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Entity type not found"; 
        } elseif (!empty($entity_type) && !in_array($entity_type,$entityTypes)) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Not valid entity type"; 
        } else {      
           
           $resposeResult = $this->test_client_entity_articles_model->getUsersClientsEntitySimilarArticles($userId,$clientId,$entity_id,$entity_type,$start_date,$end_date,$limit,$offset);
           if(!empty($resposeResult['data']))
            {
//                array_walk($resposeResult['data'], function (&$item) {
//                       $item['publish_date']=date('Y-m-d',strtotime($item['publish_datetime']));
//                    });
                $imagearchiveurl = get_archive_image_path();    
               foreach($resposeResult['data'] as $key =>$itm) {
                    $currentObjData = $resposeResult['data'][$key];
                    $resposeResult['data'][$key]['publish_date']=date('Y-m-d',strtotime($itm['publish_datetime']));
                    $created_on =strtotime(date('Y-m-d',strtotime($itm['created_on'])));
                    $isExisted = false;
                    foreach($imagearchiveurl as $imgarc) {
                         if( $created_on >= strtotime($imgarc['start_date']) && $created_on <= strtotime($imgarc['end_date']) ) {
                             $isExisted = true;
                         }else{
                             continue;
                         }
                    }
                     if( $isExisted ) {
                         $resposeResult['data'][$key]['imagebaseurl']=image_baseurl($itm);
                     } else {
                         $resposeResult['data'][$key]['imagebaseurl']=get_article_image_path(true);
                     }
                     if(!empty($resposeResult['data'][$key]['merge_unmerge_key']))
                     {
                        $groupedData = array_group_by($resposeResult['similar_articles_data'], 'merge_unmerge_key');
                        $resposeResult['data'][$key]['similar_articles'] = isset($groupedData[$resposeResult['data'][$key]['merge_unmerge_key']])?$groupedData[$resposeResult['data'][$key]['merge_unmerge_key']]:array();
                        if(!empty($resposeResult['data'][$key]['similar_articles']))
                        {
                            $resposeResult['data'][$key]['similar_articles'] = array_merge(array('0'=>$currentObjData), $resposeResult['data'][$key]['similar_articles']);
                        }
                     }
                     else
                     {
                         $resposeResult['data'][$key]['similar_articles'] = array();
                     }
                }  
            }

           $row['client_detail'] = $this->commonapi->getByWhere(base_model::$tbl_Clients, 'id', $clientId,'id,client_name,start_date,end_date,contact_person,address,address2,account_leader,department_id,contact_no,contact_email,client_logo,client_main_category,group_name,have_summary,is_active,allow_tag,hide_product_logo,hide_client_logo,has_mail_content,generate_rss_feed,dossier_logo,hide_dossier_logo,show_pdf_headline,show_word_headline,online_regional,show_sqcm_word,show_sqcm_pdf,show_sel_pub_report,hide_metadata_details');

            if ( isset($row['client_detail'][0]) && !empty($row['client_detail'][0])) {
                 $resposeResult['client_detail'] = $row['client_detail'][0];
            }
           
           $resposeResult['imagebaseurl']=get_article_image_path(true);
           $resposeResult = stripslashesArray($resposeResult);
           $resposeDataMsg = "success";
           $responseCode = HTTP_CONST::HTTP_OK;         
        }
        $resposeData = response_format($responseCode,$resposeDataMsg,$resposeResult,$postData);
        $this->response($resposeData,200);
    }
	
    function details_post(){
        
        $user_id = $this->post('user_id');
        $client_id = $this->post('client_id');
        $entity_id = $this->post('entity_id');
        $article_id = $this->post('article_id');
        $rbase_url = $this->post('rbase_url');
        
        $this->load->library('get_dependancy_data');
        
        $postData = $this->post();
        $resposeResult = [];
        $resposeDataMsg = "";
        if (empty($postData)) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Please provide valid paramters"; 
        }elseif (empty($client_id) || !is_numeric($client_id)) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Not valid client id"; 
        }elseif (empty($entity_id) || !is_numeric($entity_id)) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Not valid entity id"; 
        }elseif (empty($article_id) || !is_numeric($article_id)) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Not valid article id"; 
        } else { 
            $responseCode = HTTP_CONST::HTTP_OK;       
            $resposeDataMsg = "success";
            
            try {
                $row['entity_detail'] = $this->commonapi->getByWhere(base_model::$tbl_Entities, 'id', $entity_id,'id,entity_name,entity_display_name,entity_type,client_category,category_id,is_active');
                if ( isset($row['entity_detail'][0]) && !empty($row['entity_detail'][0])) {
                     $row['entity_detail'] = $row['entity_detail'][0];
                }
                
                $row['client_detail'] = $this->commonapi->getByWhere(base_model::$tbl_Clients, 'id', $client_id,'id,client_name,start_date,end_date,contact_person,address,address2,account_leader,department_id,contact_no,contact_email,client_logo,client_main_category,group_name,have_summary,is_active,allow_tag,hide_product_logo,hide_client_logo,has_mail_content,generate_rss_feed,dossier_logo,hide_dossier_logo,show_pdf_headline,show_word_headline,online_regional,show_sqcm_word,show_sqcm_pdf,show_sel_pub_report,hide_metadata_details');
                if ( isset($row['client_detail'][0]) && !empty($row['client_detail'][0])) {
                     $row['client_detail'] = $row['client_detail'][0];
                }
                
                if(isset($row['client_detail']['client_logo'])){
                    $row['client_detail']['client_logo'] = custom_remove_slashes(get_client_logo_path(true)).'/'.$row['client_detail']['client_logo'];
                }
                
                $row['article'] = $this->commonapi->getByWhere(base_model::$tbl_Articles, 'id', $article_id,'id,biu_article_id,feed_date,publish_date,publish_datetime,publication_id,publication,edition_id,edition,publication_type_id,publication_type,language_id,language,suppliment_id,suppliment,headline,summary,content,news_type,issue_date,source,source_id,reportor,mav,ccm,biu_publication_id,biu_edition_id,biu_language_id,biu_suppliment_id,biu_location_id,guest,author,category_id,category,position,page_no,press_release,tonality');
                if ( isset($row['article'][0]) && !empty($row['article'][0])) {
                    $row['article'] = $row['article'][0];
                } else {
                    throw new Exception("Article details not found");                    
                }
                
                $row['newsletter_article_url'] = get_newsletter_article_url($article_id,$client_id,$entity_id);
                if (isset($row['article']) && !empty($row['article'])) {
                    if(!empty($row['article']['source_id']) && !empty( $row['article']['source'] ))
                    {
                      $row['article']['source_name'] = $this->get_dependancy_data->get_source_name($row['article']['source_id'], $row['article']['source']);
                    }   
                    list($y, $m, $d) = explode("-", $row['article']['publish_date']);
                    $row['article']['publish_date'] = generic_publish_date_format($row['article']['publish_date']);
                    $articleDate = "$y/$m/$d";
                    $row['article_content'] = ( $row['article']['content'] );
                }
                $images = $this->commonapi->getAllFor(base_model::$tbl_Article_Images, 'article_id', $article_id);

                $textURL = '';
                
                // Get Mav & CCM
                $result = array();
                $mav_val = 0;
                $ccm_val = 0;
                $page_no = array();
                 
                $_imgwidthsetting = 0.333;
                $array_godrejentity = [7072,7080,7081,7082,7083,7084,7085,7086,7087,7088,7089,55944,55945,55946];
                
                $c_entity = $client_id;
                $is_godrej = 0;              
                if(in_array($c_entity,$array_godrejentity)) {
                    $_imgwidthsetting = 0.600;
                    $is_godrej = 1;                    
                }
                $row['show_images_by_godrej_size'] = $is_godrej;

                if (!empty($images)) {
                    for($i=0;$i<count($images);$i++) {
                        $imagedata = base_url() . "assets/articles_image" . $images[$i]['image'];
                        $mav    = get_number_format($images[$i]['mav']);
                        $ccm    = get_number_format($images[$i]['ccm'],2);
                        $pageNo = $image['page_no'];            
                        $imagePath = get_article_image_path(false).$images[$i]['image'];
                        $imagePathUrl = get_article_image_path(true).$images[$i]['image'];
                        $images[$i]['image_url'] = $imagePathUrl; 
                         // get image width and height in cm
                        $_row = array();
                        $_row['widthCM']  = $images[$i]['width'];
                        $_row['heightCM'] = $images[$i]['height'];
                        $_row['page_no'] = $images[$i]['page_no'];
                        $_row['mav'] = get_number_format($images[$i]['mav']);
                        $_row['ccm'] = get_number_format($images[$i]['ccm'],2);
                        $mav_val = $mav_val + (int)$images[$i]['mav'];
                        $ccm_val = $ccm_val + (int)$images[$i]['ccm'];
                        $page_no[] = $_row['page_no'];       
                        $result[] = $_row;

                        $images[$i]['display_img_width'] = (int)$images[$i]['width']*$_imgwidthsetting;
                        $images[$i]['display_img_height'] = (int)$images[$i]['height']*$_imgwidthsetting; 

                       //$row['article_image'][] = array('id'=>$image['id'],'image'=>$imagedata, 'ccm'=> $ccm, 'mav'=>$mav,'page_no' => $pageNo,'image_path'=>$imagePath );
                    }
                }
                $row['images'] = $images;
               
                $category = $this->commonapi->getAllFor(base_model::$tbl_Categories,'client_id',$client_id);
                $row['categories'] = $category;
                $row['tonality'] = get_tonality();

                $entity_articles = $this->commonapi->getAllFor(base_model::$tbl_Entity_Articles, 'article_id', $article_id);
                $get_article_entity_ids = array();
                $display_entities = [];
                if(!empty($entity_articles)){
                    $get_article_entity_ids = array_column($entity_articles,'entity_id');
                    $enitity_name = array();
                    $client_entities = $this->get_dependancy_data->getAllEntitiesForClientId($client_id);
                    $entity_articles = $this->get_dependancy_data->filterMatchedEntitiesByArticleEntities($client_entities, $get_article_entity_ids);

                    for($i=0; $i<count($entity_articles); $i++)
                    {         
                        $enitity_name[] =  $entity_articles[$i];
                        $display_entities[] = $entity_articles[$i]['entity_name'];
                        $keywords_row[] = $this->commonapi->getByField(base_model::$tbl_Entity_Keywords, 'entity_id', $entity_articles[$i]['id'] );
                        $get_article_entity_ids[] = $entity_articles[$i]['id'];
                    }
                }       

                $and_or_keywords = [];
                foreach($keywords_row as $key) {
                    if(!empty($key)) {
                        $keyword1 = $key['keyword'];
                        $keyword = str_replace('"', '', $keyword1);
                        if (strpos($keyword,'OR') != false) {
                            $and_or_keywords[] = explode(" OR ", $keyword);
                        } else if (strpos($keyword,'AND') != false) {
                            $and_or_keywords[] = explode(" AND ", $keyword);
                        } else {
                            $and_or_keywords[] = array($keyword);
                        }
                    } else {
                        $and_or_keywords[] = array();
                    }
                }

                $updated_values = $this->commonapi->searchFor(base_model::$tbl_Client_Article_Values,array(
                                                                                'article_id'=>$article_id,
                                                                                'client_id'=>$client_id,
                                                                                'entity_id'=>$entity_id
                                                                            ));
                if(!empty($updated_values) && isset($updated_values['category_id']))
                {
                    $updated_category = $this->commonapi->getByField(base_model::$tbl_Categories, 'id', $updated_values['category_id']);
                    $row['updated_category'] = $updated_category;
                }
                $row['updated_values'] = $updated_values;

                $entity_keywords = [];
                if(!empty($display_entities)){
                   
                    for($i=0; $i<count($display_entities); $i++) 
                    {
                        $entity_arr = array($display_entities[$i]);
                        $ORKeywords = array($and_or_keywords[$i]);
                        $entity_keywords[] = array_merge($entity_arr , $ORKeywords);
                    } 
                }
                $row['enitity_keywords'] = $entity_keywords;
                $row['mav_val'] = get_number_format($mav_val);
                $row['ccm_val'] = get_number_format($ccm_val);
                $row['page_no'] = implode(', ', $page_no);

                $row['publication_logo'] = "";
                $row['publication_name'] = "";
                if(isset($row['article']['publication_id']) && $row['article']['publication_id'] > 0){
                    //get entity logo....
                    $publication_data = $this->commonapi->getByField(base_model::$tbl_Publications, 'id', $row['article']['publication_id']);
                    if(!empty($publication_data)){
                        $row['publication_logo'] = $publication_data['masthead'];
                        $row['publication_name'] = $publication_data['publication'];
                    }
                }
                
                $row['readership'] = '';
                $row['circlation'] = '';
                //retrieve the redearship
                if(isset($row['article']['publication_id']) && isset($row['article']['edition_id'])){
                    $criteria = array('publication_id'=>$row['article']['publication_id'],'edition_id'=>$row['article']['edition_id']);
                    $peRow = $this->commonapi->searchFor(base_model::$tbl_Publication_Editions, $criteria);

                    if(count($peRow) > 0) {
                        $row['readership'] = $peRow[0]['readership'];
                        $row['circlation'] = get_number_format($peRow[0]['circlation']);
                    }
                    if(!is_int($row['readership']))
                        $row['readership'] = 0;
                
                }

                if(!empty($updated_values) && !empty($user_id))
                {
                    $qry="select user_id,client_id from ".base_model::$tbl_User_Clients." where client_id=".$client_id." and user_id=".$user_id;
                    $user_details=$this->commonapi->getByQuery($qry); 
                    if(empty($user_details))
                    {
                        $row['client_detail']['allow_tag']=0;
                    }
                }

                $this->load->helper('master');
                $this->cModel = $this->commonapi;
                $row['other_edition_url_html'] = get_article_view_merged_editions($row['article'], $client_id, $entity_id);
                if(!empty($rbase_url)) {
                    $row['other_edition_url_html'] = str_replace("clientportal.conceptbiu.com",$rbase_url,$row['other_edition_url_html']);
                }                
            
                $row['sqcm_val'] = ( 4 * $row['ccm_val']);
                $row['is_text_view'] = 0;

                $resposeResult = stripslashesArray($row);
            } catch (Exception $e) {
                $resposeResult = [];
                $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
                $resposeDataMsg = $e->getMessage(); 
            }

            
        }
      
        $resposeData = response_format($responseCode,$resposeDataMsg,$resposeResult,$postData);
        $this->response($resposeData,200);        
    }
    
    function pressrelease_post() {
        
        $resposeResult = [];
        $resposeDataMsg = "";
        $postData = $this->post();
        
        $client_id = $this->post('client_id');    
        $start_date = $this->post('start_date');
        $end_date = $this->post('end_date');
        $limit = $this->post('limit');
        $offset = $this->post('offset');
        $edition_ids = $this->post('edition_ids');
        $publication_ids = $this->post('publication_ids');

        if (empty($postData)) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Please provide valid paramters"; 
        }elseif (empty($client_id) || !is_numeric($client_id)) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Not valid client id"; 
        }elseif (empty($start_date) || empty($end_date)) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Start date or end date should not blank"; 
        }elseif ( ( !empty(trim($start_date)) && !empty(trim($end_date)) ) && (strtotime($end_date) < strtotime($start_date)) ) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Not valid date range"; 
        } else {  
            $responseCode = HTTP_CONST::HTTP_OK;       
            $resposeDataMsg = "success";
            $entityData = $this->entity_model->getClientEntities($client_id);
            $entityIds = "";
            if(!empty($entityData)){
                $entityIds = implode(',',array_column($entityData,'id'));
            }
            $resposeResult = $this->client_entity_articles_model->getPressReleaseArticles($client_id,$entityIds,$start_date,$end_date,$limit,$offset,$edition_ids,$publication_ids);
            $resposeResult = stripslashesArray($resposeResult);
            $resposeResult['imagebaseurl']=get_article_image_path(true);
        }
      
        $resposeData = response_format($responseCode,$resposeDataMsg,$resposeResult,$postData);
        $this->response($resposeData,200);        
    }
    function search_post(){
        $resposeResult = [];
        $resposeDataMsg = "";
        $postData = $this->post();
        
        $download_types = $this->config->item('download_types');
    
        if (empty($postData)) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Please provide valid paramters"; 
        }elseif (empty($postData['client_id']) || !is_numeric($postData['client_id'])) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Not valid client id"; 
        }elseif (empty($postData['start_date']) || empty($postData['end_date'])) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Start date or end date should not blank"; 
        }elseif ( ( !empty(trim($postData['start_date'])) && !empty(trim($postData['end_date'])) ) && (strtotime($postData['end_date']) < strtotime($postData['start_date'])) ) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Not valid date range"; 
         }else if(isset($postData['download']) && !empty($postData['download']) && !in_array($postData['download'],$download_types) ) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Not valid download type "; 
         }
         //elseif (empty($postData['media_type'])) {
        //     $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
        //     $resposeDataMsg = "Media type not found"; 
        // }  
        else {  
            $isDownload = false;
            $emailValid  = false;
            if(isset($postData['download']) && !empty($postData['download'])) {  
              $isDownload = true;  
            }
            if($isDownload && !empty($postData['email']) ) {
                $email = strtolower($postData['email']);              
                $pattern = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/'; 
                $isValid = preg_match($pattern,$email);
                if (!$isValid) {
                    $resposeDataMsg = "Not valid Email address";                     
                }else { 
                    $emailValid = true;
                }
            } 
            
            if( ( $isDownload == true && $emailValid == false ) ) {
                $responseCode = HTTP_CONST::HTTP_BAD_REQUEST; 
            } else {
                $this->load->model('articles_model','articles_model');

                $start_date= isset($postData['start_date']) ? date('Y-m-d',strtotime($postData['start_date'])) : "";
                $end_date = isset($postData['end_date']) ? date('Y-m-d',strtotime($postData['end_date'])) : "";
                $postData['daterange'] = $start_date.' - '.$end_date;
                $this->articles_model->requestData = $postData;
                $this->articles_model->keyword = isset($postData['keywords']) ? $postData['keywords'] : "";
                $this->articles_model->media_type = isset($postData['media_type']) ? $postData['media_type'] : 0;
                $this->articles_model->pubDateFrom = $start_date;
                $this->articles_model->pubDateTo = $end_date;
                $this->articles_model->publications = isset($postData['publication_id']) ? $postData['publication_id'] : "";
                $this->articles_model->publicationTypes = isset($postData['publication_type']) ? $postData['publication_type'] : "";
                $this->articles_model->editions = isset($postData['edition_id']) ? $postData['edition_id'] : [];
                
                if(empty($this->articles_model->editions)) {
                    $this->load->library('get_dependancy_data');
                    $editionIdList = $this->get_dependancy_data->getClientEditions($postData['client_id']); 
                    $editionIds = "-1";
                    if(!empty($editionIdList)){
                        if(isset($editionIdList['edition_ids'])) {
                            $this->articles_model->editions = $editionIdList['edition_ids'];
                        }
                    } 
                }

                $this->articles_model->updateParameters($postData);

                $postData['isOnlyClient'] = 1;
                $resposeResult = $this->client_entity_articles_model->getClientsEntityASearchArticles($postData,$isDownload);
                // print_r($resposeResult);
                // exit;
                $resposeResult = stripslashesArray($resposeResult);
                $resposeDataMsg = "success";
                $resposeResult['imagebaseurl']=get_article_image_path(true);
                $responseCode = HTTP_CONST::HTTP_OK;         
                if($isDownload) {                                 
                    $sendData = $this->sendDownloadLink($postData['download'],$resposeResult,$postData['email'],$postData['client_id'],$postData);
                    if(!$sendData) {
                        $resposeResult = []; 
                        $responseCode = HTTP_CONST::HTTP_BAD_REQUEST; 
                        $resposeDataMsg = "Download link not sent";
                    }else {
                        $responseCode = HTTP_CONST::HTTP_OK; 
                        $resposeDataMsg = "Download success";
                    }
                }   
            }
            
        }
        $resposeData = response_format($responseCode,$resposeDataMsg,$resposeResult,$postData);
        $this->response($resposeData,200);
    }
    
    protected function sendDownloadLink($downloadFor, $data,$to,$clientId,$postData=null) {
        $sendResult = false;
        if(empty($downloadFor) || (isset($data['data']) && empty($data['data']) ) || $clientId == "" ) {
           $sendResult = false;
        } else {
            $this->load->library('mailer');
            $dossierData = [];
            $dossierData = $data['data'];
            array_walk($dossierData, function(&$key){                 
                $key['headline'] = base64_encode($key['headline']);
                $key['summary'] = base64_encode($key['summary']);
                $key['content'] = base64_encode($key['content']);                    
                $key['view_details'] = $key['link'];
                $key['circulation'] = $key['circlation'];    
                $key['tier'] = $key['pub_tier'];    
            });
            $fetch_output = [];
            $type = "-";
            if ( $downloadFor == "word_without_mav") {
                $type = "Word";
                $dossier_data = [];
                $dossier_data = array_group_by($dossierData, function($data) {                    
                    return $data['publication_type_id']."-".$data['publication_type'];
                }  );
                
                $array_sort=array("1-Financial",'2-Mainlines',"3-Regional","4-Periodical","8-Online");
                // print_r('here');
                // exit;
                $dossier_data=sortArrayByArray($dossier_data,$array_sort);
                $dossier_data=sort_dossier_data($dossier_data);  
                try {
                    $fetch_output= $this->mailer->download_generic_word_dossier_without_mav($dossier_data, $clientId);
                } catch(Exception $e) {
                    $sendResult = false;
                }
            }
            elseif ( $downloadFor == "excel")
            {   
                $type = "Excel";
                $dossier_data = [];
                $dossier_data = array_group_by($dossierData,'article_type');
               
                try {                   
                    $fetch_output= download_excel($dossier_data, $clientId,false,true);                 
                } catch(Exception $e) {
                    $sendResult = false;
                }

            }
            
            try {
                if(isset($fetch_output['filepath']) && file_exists($fetch_output['filepath'])) {
                    $isSent = sendMailLink($to,$fetch_output['filepath'],$fetch_output['filename'],$type,$postData);
                    if($isSent) {
                        $sendResult = true;
                    }else{
                        $sendResult = false;
                    }
                } else {
                    throw new Exception("File not exist");
                }
            } catch(Exception $e) {
                $sendResult = false;
            }
        }
        return $sendResult;
    }
    
    function mailerlist_post()
    {
        $resposeResult = [];
        $resposeDataMsg = "";
        $postData = $this->post();
        $clientId = $this->post('client_id');
        $last_id = $this->post('last_id');
        
        $entityTypes = get_entity_type_list(true,true);
        if (empty($postData)) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Please provide valid paramters"; 
        }elseif (empty($clientId) || !is_numeric($clientId)) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Not valid client id"; 
        } else {      
           $this->load->model('Articles_model');
           if(empty($last_id)) {
                $last_id = 0;
           }
           $fromDateTime = date('Y-m-d');
           $toDateTime = date('Y-m-d');
           $sql = "SELECT * FROM clients WHERE is_active = 1 AND id in($clientId)";
           $clientDatas = $this->commonapi->getByQuery($sql);   
           if(empty($clientDatas)) {
                $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
                $resposeDataMsg = "Client is not active"; 
           } 
           else
           {
                $clientDatas = $clientDatas[0];
                $data = $this->Articles_model->getPrintClientMailArticlesUsingLastId($clientDatas,$fromDateTime,$toDateTime,$last_id);
               $resposeResult['data'] = [];
               if(!empty($data))
                    $resposeResult['data'] = $data;
               $resposeResult = stripslashesArray($resposeResult);
               $resposeDataMsg = "success";
               $responseCode = HTTP_CONST::HTTP_OK;
            }   
        }
        $resposeData = response_format($responseCode,$resposeDataMsg,$resposeResult,$postData);
        $this->response($resposeData,200);
    }

    public function tag_post()
    {
        $resposeResult = [];
        $resposeDataMsg = "";
        $postData = $this->post();
        $clientId = $this->post('client_id');
        $tagged_data = $this->post('tagged_data');
        $other_tagged_data = $this->post('other_tagged_data');
        $article_data = $this->post('article_data');
        
        $entityTypes = get_entity_type_list(true,true);
        if (empty($postData)) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Please provide valid paramters"; 
        }elseif (empty($clientId)) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Client detail not found"; 
        }elseif (empty($article_data)) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Articles Data is empty"; 
        }elseif (empty($tagged_data) && empty($other_tagged_data)) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "No tagged data paramters are found"; 
        }else {

            try {
                $this->load->helper('multidatabase'); 
                $this->load->helper('articles');          

                $tonality = "";
                if(isset($tagged_data['tonality'])){
                    $tonality = $tagged_data['tonality'];
                }

                $category = "";
                if(isset($tagged_data['category'])){
                    $category = $tagged_data['category'];
                }

                $direct_news = "";
                if(isset($tagged_data['direct_news'])){
                    $direct_news = $tagged_data['direct_news'];
                }
                $headline_mention = "";
                if(isset($tagged_data['headline_mention'])){
                    $headline_mention = $tagged_data['headline_mention'];
                }
                $hit_miss = "";
                if(isset($tagged_data['hit_miss'])){
                    $hit_miss = $tagged_data['hit_miss'];
                }
                $photo_mention = "";
                if(isset($tagged_data['photo_mention'])){
                    $photo_mention = $tagged_data['photo_mention'];
                }
                $spokeperson = "";
                if(isset($tagged_data['spokeperson'])){
                    $spokeperson = $tagged_data['spokeperson'];
                }
                $product_services = "";
                if(isset($tagged_data['product_services'])){
                    $product_services = $tagged_data['product_services'];
                }
                $press_release = "";
                if(isset($tagged_data['press_release'])){
                    $press_release = $tagged_data['press_release'];
                }

                $is_client_favourite = null;
                if(isset($tagged_data['is_client_favourite'])){
                    $is_client_favourite = $tagged_data['is_client_favourite'];
                    if($is_client_favourite > 0){
                        $_obj_tag['is_client_favourite'] = $is_client_favourite;
                    } else if($is_client_favourite == '-1') {
                        $_obj_tag['is_client_favourite'] = NULL;
                    }
                    
                }

                if($tonality != "")
                {   
                    if(!is_string($tonality)) {
                        throw new Exception("Tonality is not valid string value");          
                    }
                    if($tonality == '-1')
                    {
                        $tonality = NULL;
                    }
                    $_obj_tag['tonality'] = $tonality;
                }
                if($direct_news != ""){
                    if($direct_news == '-1')
                    {
                        $direct_news = NULL;
                    }
                    $_obj_tag['direct_news'] = $direct_news;
                }

                if($hit_miss != ""){
                    if($hit_miss == '-1')
                    {
                        $hit_miss = "";
                    }
                    $_obj_tag['hit_n_miss'] = $hit_miss;
                }
                if($product_services != ""){
                    if($product_services == '-1')
                    {
                        $product_services = "";
                    }
                    $_obj_tag['product_services'] = $product_services;
                }
                if($headline_mention != ""){
                     if($headline_mention == '-1')
                    {
                        $headline_mention = "";
                    }
                    $_obj_tag['headline_mention'] = $headline_mention;
                }
                if($photo_mention != ""){
                     if($photo_mention == '-1')
                    {
                        $photo_mention = "";
                    }
                    $_obj_tag['photo_mention'] = $photo_mention;
                }
                if($spokeperson != ""){
                     if($spokeperson == '-1')
                    {
                        $spokeperson = "";
                    }
                    $_obj_tag['spokeperson'] = $spokeperson;
                }

                if($press_release != ""){
                    if($press_release == '-1')
                    {
                        $press_release = "";
                    }
                    $_obj_tag['press_release_id'] = $press_release;
                }

                if($category != "")
                {
                    if($category == '-1')
                    {
                        $_obj_tag['category'] = NULL;
                        $_obj_tag['category_id'] = NULL;
                    }else{ 

                        $cat_name = isset($category['name']) ? $category['name'] : '';
                        $cat_id = isset($category['id']) ? $category['id'] : 0;
                        if(!is_string($cat_name)) {
                            throw new Exception("Category name is not valid string value");          
                        }
                        if($cat_id > 0 &&  $cat_name != "")
                        {
                            $_obj_tag['category'] = $cat_name;
                            $_obj_tag['category_id'] = $cat_id;  
                        }
                       
                    }
                } 


                if(!empty($article_data)) {
                    $headers = $this->input->request_headers();
                    $decode =  JWT::decode($headers['Authorization'], $this->config->item('jwt_key'));
                    $user = $decode->user;
                    $expUser = explode('$', $user);
                    $loggedinUserId = $expUser[0];
                    foreach ($article_data as $key => $article) {
                        $_obj = [];
                        $flag = 0;
                        $client_id = $clientId;
                        $entity_id = $article['entity_id'];
                        $article_id = $article['article_id'];
                        $media_type = $article['media_type'];
                        $pub_date = $article['pub_date'];
                        $entity_articles_id = $article['entity_articles_id'];
                        $_obj = $_obj_tag;
                        if($entity_articles_id > 0 ){
                            $_obj['entity_articles_id'] =  $entity_articles_id;
                        }

                        $criteria = array(
                        'client_id'=>$client_id,
                        'entity_id' => $entity_id,
                        'article_id' => $article_id,
                        );
                        $_obj['client_id'] = $client_id;
                        $_obj['article_id'] = $article_id;
                        $_obj['entity_id'] = $entity_id;
                        $pos = strpos($pub_date, '-');
                        if($pos) {
                            $pub_date = strtotime($pub_date);                 
                        }
                        $db = false;
                        if($pub_date > 0) {
                          $dbYear = date("Y", $pub_date);
                          $db = getYearWiseDb($dbYear);            
                        } else {
                          $dbYear = date("Y");
                          $db = getYearWiseDb($dbYear);
                        }

                        //insert for warehouse
                        insert_article_for_transfer($article_id,ucfirst($media_type));
                        
                        $cavidprint=0;
                        $cavidonline=0;

                        if($pub_date > 0) {
                          $artMonth = date("m", $pub_date);
                          $artpubDate = date("Y-m-d", $pub_date);
                          $_obj['publish_date'] = $artpubDate;
                          $_obj['month_no'] = $artMonth;
                        }

                        if(strtolower($media_type) == 'print')
                        {
                            $arrdata=$this->commonapi->SearchFor('client_article_values', $criteria,$db);
                          
                            //update if existed
                            if(count($arrdata) > 0) {
                                $_obj['last_modified_on']=date('Y-m-d H:i:s');
                                $_obj['last_modified_by']=$loggedinUserId;
                                $this->commonapi->update('client_article_values', $_obj, $criteria,$db);
                                $cavidprint=$arrdata[0]["id"];

                            } else {
                                //insert if not existed
                                $_obj['created_on']=date('Y-m-d H:i:s');
                                $_obj['created_by']=$loggedinUserId;
                                $_obj['last_modified_on']=date('Y-m-d H:i:s');
                                $_obj['last_modified_by']=$loggedinUserId;
                                $cavidprint= $this->commonapi->insert('client_article_values', $_obj,$db);                           
                            }
                           
                        }elseif (strtolower($media_type) == 'online') {
                            $arrdata=$this->commonapi->SearchFor('online_client_article_values', $criteria,$db);
                            if(count($arrdata) > 0) {
                                $_obj['last_modified_on']=date('Y-m-d H:i:s');
                                $_obj['last_modified_by']=$loggedinUserId;
                                $this->commonapi->update('online_client_article_values', $_obj, $criteria,$db);                           
                                $cavidonline=$arrdata[0]["id"];
                                
                            } else {
                                $_obj['created_on']=date('Y-m-d H:i:s');
                                $_obj['created_by']=$loggedinUserId;
                                $_obj['last_modified_on']=date('Y-m-d H:i:s');
                                $_obj['last_modified_by']=$loggedinUserId;
                                $cavidonline=$this->commonapi->insert('online_client_article_values', $_obj,$db);                            
                            }
                            
                        }

                        //other tagging
                        if(!empty($other_tagged_data)) {
                            foreach($other_tagged_data as $tagging) 
                            {
                                                                
                                $tag_id= isset($tagging['tag_attr_id']) ? $tagging['tag_attr_id'] : 0;
                                $tag_name=isset($tagging['tag_attr_name']) ? $tagging['tag_attr_name'] : "";
                                $tag_value=isset($tagging['tag_attr_value']) ? $tagging['tag_attr_value'] : '';
                                
                                $_objTagging['tag_attr_id']=$tag_id;
                                $_objTagging['tag_attr_value']=$tag_value;
                                if($tag_value!="" && $tag_value!=" " && !empty($tag_value) && $tag_id > 0)
                                {
                                       
                                    if($cavidprint!=0) //other tag print data insert
                                    {   
                                        $criteriaTagging = array(
                                        'client_article_id'=>$cavidprint,
                                        'tag_attr_id' => $tag_id
                                        );
                                        $_objTagging['client_article_id']=$cavidprint;
                                        $cnttag = $this->commonapi->countSearchFor('client_tag_attribute', $criteriaTagging,$db);
                                        if($tag_value == '-1' && $cnttag>0) {
                                            $this->commonapi->delete('client_tag_attribute', $criteriaTagging,$db);
                                        } else {
                                           
                                            if($cnttag>0)
                                            {
                                                $_objTagging['last_modified_on']=date('Y-m-d H:i:s');
                                                $_objTagging['last_modified_by']=$loggedinUserId;
                                              
                                                $this->commonapi->update('client_tag_attribute', $_objTagging, $criteriaTagging,$db);
                                    
                                            }
                                            else
                                            {
                                                $_objTagging['created_on']=date('Y-m-d H:i:s');
                                                $_objTagging['created_by']=$loggedinUserId;
                                                $_objTagging['last_modified_on']=date('Y-m-d H:i:s');
                                                $_objTagging['last_modified_by']=$loggedinUserId;
                                                $_objTagging['client_id']=$client_id;
                                                $this->commonapi->insert('client_tag_attribute', $_objTagging,$db);
                                            }  
                                        }
                                            
                                        
                                    }//cav print end

                                    if($cavidonline!=0)
                                    {   
                                        $criteriaTagging = array(
                                        'client_article_id'=>$cavidonline,
                                        'tag_attr_id' => $tag_id
                                        );
                                        $_objTagging['client_article_id']=$cavidonline;

                                        $cnttag = $this->commonapi->countSearchFor('online_client_tag_attribute', $criteriaTagging,$db);
                                        if($tag_value == '-1' && $cnttag > 0 ) {
                                            $this->commonapi->delete('online_client_tag_attribute', $criteriaTagging,$db);
                                        } else {
                                          
                                            if($cnttag>0)
                                            {
                                                $_objTagging['last_modified_on']=date('Y-m-d H:i:s');
                                                $_objTagging['last_modified_by']=$loggedinUserId;
                                                $this->commonapi->update('online_client_tag_attribute', $_objTagging, $criteriaTagging,$db);
                                    
                                            }
                                            else
                                            {
                                                $_objTagging['created_on']=date('Y-m-d H:i:s');
                                                $_objTagging['created_by']=$loggedinUserId;
                                                $_objTagging['last_modified_on']=date('Y-m-d H:i:s');
                                                $_objTagging['last_modified_by']=$loggedinUserId;
                                                $_objTagging['client_id']=$client_id;
                                                $this->commonapi->insert('online_client_tag_attribute', $_objTagging,$db);
                                    
                                            }
                                        }
                                    }// cav online end
                                }//tag empty check end

                            }//other tag foreach end
                        }//other tag if condition                   

                    }//article list loop end

                }//article list end            
                $responseCode = HTTP_CONST::HTTP_OK;
                $resposeDataMsg = "Successfully updated";
                $resposeResult = [];
            } catch (Exception $e) {
                $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;   
                $resposeDataMsg = $e->getMessage();
                $resposeResult = [];
            }

            
        }
        $resposeData = response_format($responseCode,$resposeDataMsg,$resposeResult,$postData);
        $this->response($resposeData,200);
    }

    public function keyword_list_post()
    {
        $resposeResult = [];
        $resposeDataMsg = "";
        $postData = $this->post();
        $article_id = $this->post('article_id');
        $client_id = $this->post('client_id');
        $media_type = $this->post('media_type');

        $entityTypes = get_entity_type_list(true,true);
        if (empty($postData)) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Please provide valid paramters"; 
        }elseif (empty($article_id) || !is_numeric($article_id)) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Not valid article id"; 
        }elseif (empty($client_id) || !is_numeric($client_id)) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Not valid client id"; 
        }elseif (empty($media_type)) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Not valid media type"; 
        } else {      
            try {
                $this->load->model('Articles_model');
                $articleDetails = $this->Articles_model->getArticleDetailById($article_id,$media_type);
                $entities_keywords = [];
                if(!empty($articleDetails)) {
                    $this->load->library('get_dependancy_data');
                    $client_all_entities = $this->get_dependancy_data->getAllEntitiesForClientId($client_id);
                     $online_entities_keywords = [];

                    if(!empty($client_all_entities))
                    {
                        $all_entity_ids = implode(',', $client_all_entities['all_entity_ids']);

                        if($media_type == 'online') {
                            $entity_keywords = $this->get_dependancy_data->get_online_entity_keywords($client_id, $article_id, $all_entity_ids);

                            $content = $articleDetails['content'];
                            $language_id = $articleDetails['language_id'];

                            if(!empty($entity_keywords))
                            {
                                $get_online_keyword_occurence = $this->get_dependancy_data->get_online_keyword_occurence($content, $entity_keywords);
                                if(isset($get_online_keyword_occurence['entity_names']))
                                {
                                    $entities = $get_online_keyword_occurence['entity_names'];
                                    $keywords_found = isset($get_online_keyword_occurence['keywords_found']) ? $get_online_keyword_occurence['keywords_found'] : [];
                                    $i = 0;
                                    foreach ( $entities as $key => $value) {
                                        if(isset($keywords_found[$key])) {
                                            $online_entities_keywords[$i]['entity_name'] = $value;
                                            $online_entities_keywords[$i]['keywords'] = $keywords_found[$key];
                                        }
                                        $i++;
                                    }
                                    $entities_keywords['online'] = $online_entities_keywords;
                                    
                                } else{
                                     throw new Exception("No Articles entites found");
                                }
                            } else {
                                 throw new Exception("No entities keyword found");
                            }
                        }
                        
                    } else {
                         throw new Exception("client entities not found");
                    }              

                    $resposeResult = $entities_keywords;
                    $resposeDataMsg = "success";
                    $responseCode = HTTP_CONST::HTTP_OK;
                } else {
                    throw new Exception("Article not found");                    
                }
            } catch (Exception $e) {
                $resposeDataMsg = $e->getMessage();
                $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            }
            
            
        }
        $resposeData = response_format($responseCode,$resposeDataMsg,$resposeResult,$postData);
        $this->response($resposeData,200);
    }

    function similar_article_list_all_post(){
        $resposeResult = [];
        $resposeDataMsg = "";
        $postData = $this->post();
        $userId = $this->post('user_id');
        $clientId = $this->post('client_id');
        $entity_type = $this->post('entity_type');
        $start_date = $this->post('start_date');
        $end_date = $this->post('end_date');
        $limit = $this->post('limit');
        $offset = $this->post('offset');
        $edition_ids = $this->post('edition_ids');
        $publication_ids = $this->post('publication_ids');
       
        $entityTypes = get_entity_type_list(true,true);
        if (empty($postData)) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Please provide valid paramters"; 
        }elseif (empty($clientId) || !is_numeric($clientId)) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Not valid client id"; 
        }elseif (empty($userId) || !is_numeric($userId)) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Not valid user id"; 
        }elseif (empty($start_date) || empty($end_date)) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Start date or end date should not blank"; 
        }elseif ( ( !empty(trim($start_date)) && !empty(trim($end_date)) ) && (strtotime($end_date) < strtotime($start_date)) ) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Not valid date range"; 
        }elseif (empty($entity_type)) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Entity type not found"; 
        } elseif (!empty($entity_type) && !in_array($entity_type,$entityTypes)) {
            $responseCode = HTTP_CONST::HTTP_BAD_REQUEST;
            $resposeDataMsg = "Not valid entity type"; 
        } else {      
           $publication_type_ids = $this->post('publication_type_ids');
         
           $client_entity_ids = 0;
            $competior_entity_ids = 0;
            $industry_entity_ids = 0;
            $this->load->library('get_dependancy_data');

            $allEntitties = $this->get_dependancy_data->getAllEntitiesForClientId($clientId);
            $entityTypes = get_entity_type_list(false,$case=true); 
            if(!empty($allEntitties)) {
                if ($entity_type == ($entityTypes['CLIENT'])) {//get client entities           
                    if(isset($allEntitties['client_entity_ids'])) {
                        $client_entity_ids = implode(',', $allEntitties['client_entity_ids']);
                    }
                } elseif($entity_type == ($entityTypes['COMPETITOR'])) {//get COMPETITOR entities
                    if(isset($allEntitties['competitor_entity_ids'])) {
                        $competior_entity_ids = implode(',', $allEntitties['competitor_entity_ids']);
                    }
                } elseif($entity_type == ($entityTypes['INDUSTRY'])) {//get INDUSTRY entities
                    if(isset($allEntitties['industry_entity_ids'])) {
                        $industry_entity_ids = implode(',', $allEntitties['industry_entity_ids']);
                    }
                }elseif($entity_type == ($entityTypes['ALL'])) {//get INDUSTRY entities
                    if(isset($allEntitties['client_entity_ids'])) {
                        $client_entity_ids = implode(',', $allEntitties['client_entity_ids']);
                    }
                    if(isset($allEntitties['competitor_entity_ids'])) {
                        $competior_entity_ids = implode(',', $allEntitties['competitor_entity_ids']);
                    }
                    if(isset($allEntitties['industry_entity_ids'])) {
                        $industry_entity_ids = implode(',', $allEntitties['industry_entity_ids']);
                    }
                }
            }

           $resposeResult = $this->client_entity_articles_model->getUsersClientsEntitySimilarArticlesAll($userId,$clientId,$client_entity_ids,$competior_entity_ids,$industry_entity_ids,$entity_type,$start_date,$end_date,$limit,$offset,$edition_ids,$publication_ids,$publication_type_ids);
           if(!empty($resposeResult['data']))
            {
//                array_walk($resposeResult['data'], function (&$item) {
//                       $item['publish_date']=date('Y-m-d',strtotime($item['publish_datetime']));
//                    });
                $imagearchiveurl = get_archive_image_path();    
               foreach($resposeResult['data'] as $key =>$itm) {
                    $currentObjData = $resposeResult['data'][$key];
                    $resposeResult['data'][$key]['publish_date']=date('Y-m-d',strtotime($itm['publish_datetime']));
                    $created_on =strtotime(date('Y-m-d',strtotime($itm['created_on'])));
                    $isExisted = false;
                    foreach($imagearchiveurl as $imgarc) {
                         if( $created_on >= strtotime($imgarc['start_date']) && $created_on <= strtotime($imgarc['end_date']) ) {
                             $isExisted = true;
                         }else{
                             continue;
                         }
                    }
                     if( $isExisted ) {
                         $resposeResult['data'][$key]['imagebaseurl']=image_baseurl($itm);
                     } else {
                         $resposeResult['data'][$key]['imagebaseurl']=get_article_image_path(true);
                     }
                     if(!empty($resposeResult['data'][$key]['merge_unmerge_key']))
                     {
                        $groupedData = array_group_by($resposeResult['similar_articles_data'], 'merge_unmerge_key');
                        $resposeResult['data'][$key]['similar_articles'] = isset($groupedData[$resposeResult['data'][$key]['merge_unmerge_key']])?$groupedData[$resposeResult['data'][$key]['merge_unmerge_key']]:array();
                        if(!empty($resposeResult['data'][$key]['similar_articles']))
                        {
                            $resposeResult['data'][$key]['similar_articles'] = array_merge(array('0'=>$currentObjData), $resposeResult['data'][$key]['similar_articles']);
                        }
                     }
                     else
                     {
                         $resposeResult['data'][$key]['similar_articles'] = array();
                     }
                }  
            }

           $row['client_detail'] = $this->commonapi->getByWhere(base_model::$tbl_Clients, 'id', $clientId,'id,client_name,start_date,end_date,contact_person,address,address2,account_leader,department_id,contact_no,contact_email,client_logo,client_main_category,group_name,have_summary,is_active,allow_tag,hide_product_logo,hide_client_logo,has_mail_content,generate_rss_feed,dossier_logo,hide_dossier_logo,show_pdf_headline,show_word_headline,online_regional,show_sqcm_word,show_sqcm_pdf,show_sel_pub_report,hide_metadata_details');

            if ( isset($row['client_detail'][0]) && !empty($row['client_detail'][0])) {
                 $resposeResult['client_detail'] = $row['client_detail'][0];
            }
           
           $resposeResult['imagebaseurl']=get_article_image_path(true);
           $resposeResult = stripslashesArray($resposeResult);
           $resposeDataMsg = "success";
           $responseCode = HTTP_CONST::HTTP_OK;         
        }
        $resposeData = response_format($responseCode,$resposeDataMsg,$resposeResult,$postData);
        $this->response($resposeData,200);
    }
}
