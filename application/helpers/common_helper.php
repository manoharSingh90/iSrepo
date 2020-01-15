<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function NPS_old($campaign_id)
{
  $CI =& get_instance();
  $query =$CI->db->query("SELECT
    CASE 
    WHEN user_reviews.rating >= 7 THEN 'Promoter'
    WHEN user_reviews.rating <= 4 THEN 'Passive'
    WHEN user_reviews.rating > 4 AND user_reviews.rating < 7 THEN 'Detractor'
    ELSE 'Error'
    END AS np_segment
    , COUNT(*) as count
    FROM
    user_reviews JOIN campaigns ON campaigns.review_id=user_reviews.review_id WHERE campaigns.id=".$campaign_id." and user_reviews.is_campaign_review='1'
    GROUP BY 1");  
  $npsData=$query->result_array();

  $nps=0;
  if($npsData){
    if(array_key_exists(0, $npsData))
      $detractor=$npsData[0]['count'];
    else
      $detractor=0;

    if(array_key_exists(1, $npsData))
      $passive=$npsData[1]['count'];
    else
      $passive=0;
    if(array_key_exists(2, $npsData))
      $promoter=$npsData[2]['count'];
    else
      $promoter=0;

   // $passive=$npsData[1]['count'];
  //  $promoter=$npsData[2]['count'];
   // $error=$npsData[3]['count'];
    $totalReviews=($promoter+$passive+$detractor);
    $nps=round((($promoter-$passive)*100)/$totalReviews,2);
  }
  return $nps;
}

function NPS($campaign_id)
{
  $CI =& get_instance();
  $query =$CI->db->query("SELECT
    CASE 
    WHEN user_reviews.rating >= 8.5 THEN 'Promoter'
    WHEN user_reviews.rating <= 6 THEN 'Passive'
    WHEN user_reviews.rating > 6 AND user_reviews.rating < 8.5 THEN 'Detractor'
    ELSE 'Error'
    END AS np_segment
    , COUNT(*) as count
    FROM
    user_reviews JOIN campaigns ON campaigns.review_id=user_reviews.review_id WHERE campaigns.id=".$campaign_id." and user_reviews.is_campaign_review='1'
    GROUP BY 1");  
  $npsData=$query->result_array();

  $nps=0;
  if($npsData){
    if(array_key_exists(0, $npsData))
      $detractor=$npsData[0]['count'];
    else
      $detractor=0;

    if(array_key_exists(1, $npsData))
      $passive=$npsData[1]['count'];
    else
      $passive=0;
    if(array_key_exists(2, $npsData))
      $promoter=$npsData[2]['count'];
    else
      $promoter=0;

   // $passive=$npsData[1]['count'];
  //  $promoter=$npsData[2]['count'];
   // $error=$npsData[3]['count'];
    $totalReviews=($promoter+$passive+$detractor);
    $nps=round((($promoter-$passive)*100)/$totalReviews,2);
  }
  return $nps;
}