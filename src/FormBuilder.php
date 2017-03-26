<?php
namespace niklaslu;

/**
 * Class FormBuilder
 * @package niklaslu
 *
 *
 * config 配置项
 *      ns ：命名空间
 *      name : 表单名称
 *      form_class : 表单的class, array
 *      action ： 请求地址
 *      method ： 请求方式 GET POST 默认POST
 *      upload_file : 是否上传文件
 *      is_build_form_element : 是否构建form元素
 */
class FormBuilder {

    // 表单名称
    private $formName = 'form';

    // 命名空间
    private $formNs = '';

    // 配置
    private $config = [];

    // 请求地址
    private $action = '';

    // 请求方式
    private $method = 'POST';

    // 表单数据编码
    private $enctype = 'application/x-www-form-urlencoded';

    // 表单class
    private $formClass = ['form'];

    // 是否窗帘 form 元素
    private $isBuildFormElement = true;

    // 表单元素
    private $formElements = [];

    // 表单分组
    private $formGroups = [];

    public function __construct($config = [])
    {

        if (isset($config['name'])){
            $this->formName = $config['name'];
        }

        if (isset($config['ns']) && $config['ns']){
            $this->formNs = $config['ns'] . '-' ;
        }

        if (isset($config['form_class'])){
            $this->setFormClass($config['form_class']);
        }

        // 设置是否生成form元素
        if (isset($config['is_build_form_element']) && $config['is_build_form_element'] == true){
            $this->isBuildFormElement = $config['is_build_form_element'];
        }

        if (isset($config['action'])){
            $this->action = $config['action'];
        }

        if (isset($config['method'])){
            $this->method = $config['method'];
        }

        if (isset($config['upload_file']) && $config['upload_file'] == true){
            $this->enctype = 'multipart/form-data';
        }

        return $this;

    }

    public function getConfig(){

        return $this->config;
    }

    public function setConfig($config){

        $this->config = $config;

        return $this;
    }

    /**
     * 设置表单的名称
     * @param $formName
     * @return $this
     */
    public function setFormName($formName){

        $this->formName = $formName;

        return $this;

    }

    /**
     * 设置表单namespace
     * @param $formNs
     * @return $this
     */
    public function setFormNs($formNs){

        $this->formNs = $formNs ? $formNs .'-' : '';

        return $this;
    }
    /**
     * 设置表单的class
     * @param $formClass
     */
    protected function setFormClass($formClass){

        if (is_string($formClass)){
            $formClass = explode(" " , $formClass);
        }

        $this->formClass = $formClass;
    }

    /**
     * 设置action
     * @param $action
     * @return $this
     */
    public function setAction($action){

        $this->action = $action;

        return $this;
    }

    /**
     * 设置method
     * @param $method
     * @return $this
     */
    public function setMethod($method){

        $method = strtoupper($method);
        if ($method == 'GET' || $method == 'POST'){

            $this->method = $method;
        }

        return $this;
    }



    /**
     * 设置表单上传文件
     * @return $this
     */
    public function uploadFile(){

        $this->enctype = 'multipart/form-data';

        return $this;
    }

    /**
     * 添加表单的class
     * @param $className
     */
    public function addClass($className){

        $formClass = $this->getClassName();
        if (is_string($formClass)){
            $formClass = explode(" ", $formClass);
        }
        $formClass[] = $className;

        $this->setFormClass($formClass);

        return $this;
    }

    /**
     * 获取表单的class
     * @param string $format
     * @return array|string
     */
    public function getClassName($format = 'arr'){

        $formClass = $this->formClass;

        if ($format == 'str'){
            return implode(" " , $formClass);
        }else {
            return $formClass;
        }
    }

    /**
     * 添加所有组
     * @param $groups
     * @return $this
     */
    public function addGroups($data){

        foreach ($data as $k=>$v){
            $groups[] = ['name' => $k , 'title' => $v];
        }
        $this->formGroups = $groups;
        return $this;
    }

    /**
     * 添加单个组
     * @param $groupName
     * @param $groupTitle
     * @return $this
     */
    public function addGroup($groupName , $groupTitle){

        $groups = $this->formGroups;
        $groups[] = ['name' => $groupName , 'title' => $groupTitle];

        $this->formGroups = $groups;
        return $this;
    }
    /**
     * 构建html
     * @return string
     */
    public function build(){

        $html = '';
        if ($this->isBuildFormElement == true){
            $html .= $this->buildHeader();
        }

        $html .= $this->buildElements();

        if ($this->isBuildFormElement == true){
            $html .= "</form>";
        }

        return $html;
    }

    /**
     * 构建表单form element
     * @return string
     */
    private function buildHeader(){

        $formClass = $this->getClassName('str');
        $formName = $this->formName;
        $formNs = $this->formNs;

        $action = $this->action;
        $method = $this->method;
        $enctype = $this->enctype;
        $header = "<form action='".$action."' method='".$method."' class='".$formNs.$formClass."' id='".$formNs.$formName."' name='".$formNs.$formName."' enctype='".$enctype."'>";

        return $header;
    }

    /**
     * 构建表单元素
     * @return string
     */
    private function buildElements(){

        $eles = $this->getElements();
        $groups = $this->elementsGroup($eles);
        $formNs = $this->formNs;

        $elementsHtml = '';
        foreach ($groups as $k=>$v){
            $elements = isset($v['elements']) ? $v['elements'] : null;
            if ($elements){

                if ($k != 'base'){
                    $elementsHtml .= '<fieldset class="'.$formNs.'form-field'.$k.'" id="'.$formNs.'form-field-'.$k.'" >';
                    if (isset($v['title']) && $v['title']){
                        $elementsHtml .= '<legend>'.$v['title'].'</legend>';
                    }

                }
                foreach ($elements as $ele){
                    $type = $ele['type'];
                    if ($type == 'text' || $type == 'hidden' || $type == 'password' || $type == 'email' || $type == 'file' || $type =='button' || $type == 'number'){
                        $elementsHtml .= $this->buildInput($ele);
                    }
                    if ($type == 'textarea') {
                        $elementsHtml .= $this->buildTextarea($ele);
                    }
                    if ($type == 'select'){
                        $elementsHtml .= $this->buildSelect($ele);
                    }
                    if ($type == 'radio'){
                        $elementsHtml .= $this->buildRadio($ele);
                    }
                    if ($type == 'checkbox'){
                        $elementsHtml .= $this->buildCheckbox($ele);
                    }
                    if ($type == 'submit' || $type == 'reset'){
                        $elementsHtml .= $this->buildSubmit($ele);
                    }
                }
                if ($k != 'base'){
                    $elementsHtml .= '</fieldset>';
                }


            }
        }




        return $elementsHtml;
    }

    /**
     * 构建表单 textarea element
     * @param $ele
     * @return string
     */
    private function buildTextarea($ele) {

        $formNs = $this->formNs;

        if ($ele['editor'] == true){
            $textareaClass = $formNs.'form-editor '.$formNs.'form-textarea '.$formNs.'textarea-'.$ele['name'];
        }else {
            $textareaClass = $formNs.'form-textarea '.$formNs.'textarea-'.$ele['name'];
        }

        $html = '<div class="'.$formNs.'form-group">';
        if ($ele['title']){
            $html .= '<label class="'.$formNs.'form-label" for="'.$ele['id'].'">'.$ele['title'].'</label>';
        }
        $html .= '<div class="'.$formNs.'form-content">';
        $html .= '<textarea name="'.$ele['name'].'" id="'.$ele['id'].'" class="'.$textareaClass.'" placeholder="'.$ele['placeholder'].'" ';
        if (isset($ele['required'])){
            $html .= ' required="" ';
        }
        if (isset($ele['readonly'])){
            $html .= ' readonly="" ';
        }
        $html .= '>';
        $html .= $ele['value'];
        $html .= "</textarea>";
        if ($ele['msg']){
            $html .= '<p class="'.$formNs.'form-help">'.$ele['msg'].'</p>';
        }

        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    /**
     * 构建表单 input element
     * @param $ele
     * @return string
     */
    private function buildInput($ele){

        $formNs = $this->formNs;

        $html = '<div class="'.$formNs.'form-group">';
        if ($ele['title']){
            $html .= '<label class="'.$formNs.'form-label" for="'.$ele['id'].'">'.$ele['title'].'</label>';
        }
        $html .= '<div class="'.$formNs.'form-content">';
        $html .= '<input type="'.$ele['type'].'"  name="'.$ele['name'].'" id="'.$ele['id'].'" value="'.$ele['value'].'" placeholder="'.$ele['placeholder'].'" ';
        if (isset($ele['required'])){
            $html .= ' required="" ';
        }
        if (isset($ele['readonly'])){
            $html .= ' readonly="" ';
        }
        $html .= '>';
        if ($ele['msg']){
            $html .= '<p class="'.$formNs.'form-help">'.$ele['msg'].'</p>';
        }
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    /**
     * 构建表单 select element
     * @param $ele
     * @return string
     */
    private function buildSelect($ele){

        $formNs = $this->formNs;

        $html = '<div class="'.$formNs.'form-group">';
        if ($ele['title']){
            $html .= '<label class="'.$formNs.'form-label" for="'.$ele['id'].'">'.$ele['title'].'</label>';
        }
        $html .= '<div class="'.$formNs.'form-content">';
        $html .= '<select name="'.$ele['name'].'" id="'.$ele['id'].'" class="'.$formNs.'form-textarea textarea-'.$ele['name'].'" ';
        if (isset($ele['required'])){
            $html .= ' required="" ';
        }
        if (isset($ele['readonly'])){
            $html .= ' readonly="" ';
        }

        $html .= '>';

        foreach ($ele['extra'] as $k=>$v){
            if ($ele['value'] == $k){
                $html .= '<option value="'.$k.'" selected="" >'.$v.'</option>';
            }else {
                $html .= '<option value="'.$k.'" >'.$v.'</option>';
            }
        }

        $html .= '</select>';
        if ($ele['msg']){
            $html .= '<p class="'.$formNs.'form-help">'.$ele['msg'].'</p>';
        }
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    /**
     * 构建表单 radio element
     * @param $ele
     * @return string
     */
    private function buildRadio($ele){

        $formNs = $this->formNs;

        $html = '<div class="'.$formNs.'form-group">';
        if ($ele['title']){
            $html .= '<label class="'.$formNs.'form-label" for="'.$ele['id'].'">'.$ele['title'].'</label>';
        }
        $html .= '<div class="'.$formNs.'form-content">';

        foreach ($ele['extra'] as $k=>$v){
            if ($ele['value'] == $k){
                $html .= '<label class="'.$formNs.'form-radio"><input type="radio" name="'.$ele['name'].'" value="'.$k.'" checked="" >'.$v.'</label>';
            }else {
                $html .= '<label class="'.$formNs.'form-radio"><input type="radio" name="'.$ele['name'].'" value="'.$k.'" >'.$v.'</label>';
            }
        }

        if ($ele['msg']){
            $html .= '<p class="'.$formNs.'form-help">'.$ele['msg'].'</p>';
        }
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    /**
     * 构建表单checkbox element
     * @param $ele
     * @return string
     */
    private function buildCheckbox($ele){

        $formNs = $this->formNs;

        $html = '<div class="'.$formNs.'form-group">';
        if ($ele['title']){
            $html .= '<label class="'.$formNs.'form-label" for="'.$ele['id'].'">'.$ele['title'].'</label>';
        }
        $html .= '<div class="'.$formNs.'form-content">';

        if (is_string($ele['value'])){
            $ele['value'] = $ele['value'] ? explode(',' , $ele['value']) : [];
        }

        foreach ($ele['extra'] as $k=>$v){
            if (in_array($k , $ele['value'])){
                $html .= '<label class="'.$formNs.'form-checkbox"><input type="checkbox" name="'.$ele['name'].'" value="'.$k.'" checked="" >'.$v.'</label>';
            }else {
                $html .= '<label class="'.$formNs.'form-checkbox"><input type="checkbox" name="'.$ele['name'].'" value="'.$k.'" >'.$v.'</label>';
            }
        }

        if ($ele['msg']){
            $html .= '<p class="'.$formNs.'form-help">'.$ele['msg'].'</p>';
        }
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    /**
     * 构建submit 按钮
     * @param $ele
     * @return string
     */
    private function buildSubmit($ele){

        $formNs = $this->formNs;

        $html = '<div class="'.$formNs.'form-group '.$ele['type'].'">';
        $html .= '<input type="'.$ele['type'].'" value="'.$ele['value'].'" >';
        $html .= '</div>';

        return $html;
    }


    /**
     * 添加元素
     * @param $element
     * @return $this
     */
    public function addElement($element){

        $elements = $this->formElements;
        $elements[] = $element;

        $this->formElements = $elements;

        return $this;
    }

    /**
     * 获取表单元素
     * @return array
     */
    public function getElements(){

        $elements = $this->formElements;
        return $elements;
    }

    /**
     * 分组表单元素
     * @param $elements
     * @return array
     */
    public function elementsGroup($elements){

        $groups = $this->formGroups;
        $groupNames = [];
        $groupEles = [];
        foreach ($groups as $k=>$v){
            $groupNames[] = $k;
            $groupEles[$v['name']] = ['title'=>$v['title']];
        }

        foreach ($elements as $ele){
            if (isset($ele['group']) && $ele['group']){
                $group = $ele['group'];
                if (in_array($group , $groupNames)){
                    $groupEles[$group]['elements'][] = $ele;
                }else{
                    $groupEles['base']['elements'][] = $ele;
                }
            }else {
                $groupEles['base']['elements'][] = $ele;
            }

        }

        return $groupEles;
    }

    /**
     * 添加text元素
     * @param $name
     * @param string $value
     * @param string $placeholder
     * @param bool $required
     * @param bool $readonly
     * @return $this
     */
    public function addText($name , $title = '',$value = '' , $placeholder = '' , $required = false , $msg = '' ,$group = '',$readonly = false){

        $this->addInput('text' , $name , $title ,$value  , $placeholder  , $required  , $msg ,$group , $readonly );
        return $this;
    }

    public function addHidden($name , $title = '',$value = '' , $placeholder = '' , $required = false ,$msg = '' , $group = '', $readonly = false){

        $this->addInput('hidden' , $name ,$title , $value  , $placeholder  , $required  , $msg , $group , $readonly );

        return $this;

    }

    public function addPassword($name , $title = '',$value = '' , $placeholder = '' , $required = false , $msg = '' ,$group = '',$readonly = false){

        $this->addInput('password' , $name , $title ,$value  , $placeholder  , $required  ,$msg ,$group , $readonly );
        return $this;
    }

    public function addEmail($name , $title = '',$value = '' , $placeholder = '' , $required = false ,$msg = '' , $group = '',$readonly = false){

        $this->addInput('email' , $name , $title ,$value  , $placeholder  , $required  ,$msg ,$group , $readonly );
        return $this;
    }

    public function addNumber($name , $title = '',$value = '' , $placeholder = '' , $required = false , $msg = '' ,$group = '',$readonly = false){

        $this->addInput('number' , $name , $title ,$value  , $placeholder  , $required  ,$msg , $group ,$readonly );
        return $this;
    }

    public function addFile($name , $title = '',$value = '' , $placeholder = '' , $required = false , $msg = '' ,$group = '',$readonly = false){
        $this->addInput('file' , $name , $title ,$value  , $placeholder  , $required  , $msg ,$group ,$readonly );
        return $this;
    }

    public function addButton($name , $title = '',$value = '' , $placeholder = '' , $required = false , $msg = '' ,$group = '',$readonly = false){
        $this->addInput('button' , $name , $title ,$value  , $placeholder  , $required  , $msg ,$group ,$readonly );
        return $this;
    }


    /**
     * 添加input
     * @param string $type
     * @param $name
     * @param string $value
     * @param string $placeholder
     * @param bool $required
     * @param bool $readonly
     * @return $this
     */
    public function addInput($type = 'text' ,$name , $title ='' ,$value = '' , $placeholder = '' , $required = false , $msg = '', $group = '' , $readonly = false){

        $data = [

            'type' => $type,
            'name' => $name,
            'title' => $title,
            'id'  => $this->formName . '-' . $name ,
            'placeholder' => $placeholder,
            'value' => $value,
            'msg' => $msg,
            'group' => $group
        ];

        if ($required == true){
            $data['required'] = $required;
        }
        if ($readonly == true){
            $data['readonly'] = $readonly;
        }

        $this->addElement($data);

        return $this;
    }

    public function addTextarea($name , $title ='' ,$value = '' , $placeholder = '' , $editor = false , $required = false , $msg = '', $group = '' , $readonly = false) {

        $data = [

            'type' => 'textarea',
            'name' => $name,
            'title' => $title,
            'id'  => $this->formName . '-' . $name ,
            'placeholder' => $placeholder,
            'value' => $value,
            'msg' => $msg,
            'group' => $group,
            'editor' => $editor
        ];

        if ($required == true){
            $data['required'] = $required;
        }
        if ($readonly == true){
            $data['readonly'] = $readonly;
        }

        $this->addElement($data);

        return $this;

    }

    /**
     * 添加select
     * @param $name
     * @param string $title
     * @param string $value
     * @param array $extra
     * @param bool $required
     * @param bool $readonly
     * @return $this
     */
    public function addSelect($name , $title ='' ,$value = '' , $extra = [] , $required = false , $msg ='' , $group = '' , $readonly = false){

        $this->addChoose('select' , $name , $title ,$value , $extra , $required , $msg , $group ,$readonly );
        return $this;
    }

    /**
     * 添加radio
     * @param $name
     * @param string $title
     * @param string $value
     * @param array $extra
     * @param bool $required
     * @param bool $readonly
     * @return $this
     */
    public function addRadio($name , $title ='' ,$value = '' , $extra = [] , $required = false , $msg = '', $group = '' , $readonly = false){
        $this->addChoose('radio' , $name , $title ,$value , $extra , $required , $msg , $group ,$readonly );
        return $this;
    }

    /**
     * 添加checkbox
     * @param $name
     * @param string $title
     * @param string $value
     * @param array $extra
     * @param bool $required
     * @param bool $readonly
     * @return $this
     */
    public function addCheckbox($name , $title ='' ,$value = '' , $extra = [] , $required = false , $msg = '' ,$group = '' , $readonly = false){
        $this->addChoose('checkbox' , $name , $title ,$value , $extra , $required , $msg , $group , $readonly );
        return $this;
    }

    /**
     * 添加选择
     * @param $type
     * @param $name
     * @param string $title
     * @param string $value
     * @param array $extra
     * @param bool $required
     * @param bool $readonly
     * @return $this
     */
    public function addChoose($type , $name , $title ='' ,$value = '' , $extra = [] , $required = false , $msg = '', $group = '' ,$readonly = false){

        $data = [

            'type' => $type,
            'name' => $name,
            'title' => $title,
            'id'  => $this->formName . '-' . $name ,
            'extra' => $extra,
            'value' => $value,
            'msg'  => $msg,
            'group' => $group
        ];

        if ($required == true){
            $data['required'] = $required;
        }
        if ($readonly == true){
            $data['readonly'] = $readonly;
        }

        $this->addElement($data);

        return $this;
    }
    /**
     * 添加submit
     * @param string $value
     */
    public function addSubmit($value = 'submit'){

        $data = ['type'=>'submit' , 'value' => $value];

        $this->addElement($data);

        return $this;
    }

    /**
     * 添加reset
     * @param string $value
     * @return $this
     */
    public function addReset ($value = 'reset'){

        $data = ['type'=>'reset' , 'value' => $value];

        $this->addElement($data);

        return $this;
    }
}
?>
