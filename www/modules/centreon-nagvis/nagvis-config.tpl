{$form.javascript}
<form {$form.attributes}>
    <div id="validFormTop">
        <p class="oreonbutton">{$form.submitC.html}&nbsp;&nbsp;&nbsp;{$form.reset.html}</p>
    </div>
    <div id='tab1' class='tab'>
        <table class="ListTable">
            <tr class="ListHeader"><td class="FormHeader" colspan="2"><img src='./img/icones/16x16/tool.gif'>&nbsp;&nbsp;{$form.header.title}</td></tr>
            <tr class="list_lvl_1"><td class="ListColLvl1_name" colspan="2"><img src='./img/icones/16x16/text_code.gif'>&nbsp;&nbsp;{$form.header.information}</td></tr>
            <tr class="list_one"><td class="FormRowField">{$form.centreon_nagvis_uri.label}</td><td class="FormRowValue">{$form.centreon_nagvis_uri.html}</td></tr>
            <tr class="list_two"><td class="FormRowField">{$form.centreon_nagvis_path.label}</td><td class="FormRowValue">{$form.centreon_nagvis_path.html}</td></tr>
            <tr class="list_lvl_2"><td class="ListColLvl2_name" colspan="2">{$form.required._note}</td></tr>
        </table>
    </div>
    <div id="validForm">
        <p class="oreonbutton">{$form.submitC.html}&nbsp;&nbsp;&nbsp;{$form.reset.html}</p>
    </div>
    {$form.hidden}
</form>
