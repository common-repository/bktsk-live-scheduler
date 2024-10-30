(function($) {
  var $wpInlineEdit = inlineEditPost.edit;
  inlineEditPost.edit = function(id) {
    $wpInlineEdit.apply(this, arguments);
    var $post_id = 0;
    if (typeof id == "object") $post_id = parseInt(this.getId(id));
    if ($post_id > 0) {
      var $edit_row = $("#edit-" + $post_id);
      var $post_row = $("#post-" + $post_id);
      //bktsk_yt_live_url
      var $chair = $(".column-bktsk_yt_live_url", $post_row).html();
      if ("none" != $chair) {
        $(':input[name="bktsk_yt_live_url"]', $edit_row).val($chair);
      }
    }
  };
})(jQuery);
