/**
 * Confirme delete - used to confirm delete in symfony forms
 * overrides default Javascript confirm delete dialog with jQuery UI
 * @param string message
 * @param object atag The <a> tag element itself
 * @param string csrf_token_field_name The csrf_token fieldname
 * @param string csrf_token The csrf_token
 * @author Rafael Goulart <rafaelgou@gmail.com>
 */
function deleteConfirm(message, atag, csrf_token_field_name, csrf_token)
{
	var answer = confirm(message)
	if (answer){
      var f = document.createElement('form');
      f.style.display = 'none';
      document.body.appendChild(f);
      f.method = 'post';
      f.action = atag.href;
      var m = document.createElement('input');
      m.setAttribute('type', 'hidden');
      m.setAttribute('name', 'sf_method');
      m.setAttribute('value', 'delete');
      f.appendChild(m);
      if ( typeof( csrf_token_field_name ) != 'undefined' && typeof( csrf_token ) != 'undefined')
      {
        var m = document.createElement('input');
        m.setAttribute('type', 'hidden');
        m.setAttribute('name', csrf_token_field_name);
        m.setAttribute('value', csrf_token);
        f.appendChild(m);
        f.submit();
      }
	}
    return false;
}

function activateDetails()
{
    /**
     * Activates onmouseover details under <tr> tags
     * @author Rafael Goulart <rafaelgou@gmail.com>
     */
    $('tr,.has_details').hover(
      function() {
        $(this).find('.details').show();
        $(this).find('.details_fixed').addClass('details_fixed_show');
      },
      function() {
        $(this).find('.details').hide();
        $(this).find('.details_fixed').removeClass('details_fixed_show');
      }
    );

}

window.onload = function()
{
    var els = document.getElementsByClassName('has_details');
    for (var i=0;i<els.length;i++) {
        els[i].onmouseover = function() {
            this.style.backgroundColor = 'gainsboro';
            for (var j=0;j<this.children.length;j++) {
                var child = this.children[j].style.visibility
                child.style.visibility = 'visible';
            };
        };
        els[i].onmouseout = function() {
            this.style.backgroundColor = 'white';
        };
    }
};
