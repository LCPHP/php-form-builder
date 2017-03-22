<?php
namespace niklaslu;

/**
 * Class FormBuilder
 * @package niklaslu
 *
 *
 * config 配置项
 *      name : 表单名称
 *      form_class : 表单的class, array
 *      action ： 请求地址
 *      method ： 请求方式 GET POST 默认POST
 *      upload_file : 是否上传文件
 *      is_build_form_element : 是否构建form元素
 */
class FormBuilder {

    private $formName = '';

    private $config = [];

    private $action = '';

    private $method = 'POST';

    private $enctype = 'application/x-www-form-urlencoded';

    private $formClass = [];

    private $isBuildFormElement = true;

    private $formElements = [];

    private $formGroups = [];

    private $formHtml = [];

    public function __construct($config = [])
    {
        if (isset($config['name'])){
            $this->formName = $config['name'];
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

    public function getClassName($format = 'arr'){

        $formClass = $this->formClass;

        if ($format == 'str'){
            return implode(" " , $formClass);
        }else {
            return $formClass;
        }
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
     * 构建form元素
     * @return string
     */
    private function buildHeader(){

        $formClass = $this->getClassName('str');
        $formName = $this->formName;

        $action = $this->action;
        $method = $this->method;
        $enctype = $this->enctype;
        $header = "<form action='".$action."' method='".$method."' class='".$formClass."' id='".$formName."' name='".$formName."' enctype='".$enctype."'>";

        return $header;
    }

    private function buildElements(){

        $elements = $this->getElements();

        $elementsHtml = '';
        foreach ($elements as $ele){
            $type = $ele['type'];
            if ($type == 'text' || $type == 'hidden' || $type == 'password' || $type == 'email' || $type == 'file' || $type =='image' || $type =='button'){
                $elementsHtml .= $this->buildInput($ele);
            }
            if ($type == 'textarea') {
                $elementsHtml .= $this->buildTextarea($ele);
            }
            if ($type == 'submit' || $type == 'reset'){
                $elementsHtml .= $this->buildSubmit($ele);
            }
        }

        return $elementsHtml;
    }

    private function buildTextarea($ele) {

        $html = '<div class="form-group">';
        if ($ele['title']){
            $html .= '<label class="form-label" for="'.$ele['id'].'">'.$ele['title'].'</label>';
        }
        $html .= '<div class="form-content">';
        $html .= '<textarea name="'.$ele['name'].'" id="'.$ele['id'].'" class="form-textarea textarea-'.$ele['name'].'" placeholder="'.$ele['placeholder'].'" ';
        if (isset($ele['required'])){
            $html .= ' required="" ';
        }
        if (isset($ele['readonly'])){
            $html .= ' readonly="" ';
        }
        $html .= ">";
        $html .= $ele['value'];
        $html .= "</textarea>";
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    private function buildInput($ele){

        $html = '<div class="form-group">';
        if ($ele['title']){
            $html .= '<label class="form-label" for="'.$ele['id'].'">'.$ele['title'].'</label>';
        }
        $html .= '<div class="form-content">';
        $html .= '<input type="'.$ele['type'].'"  name="'.$ele['name'].'" id="'.$ele['id'].'" value="'.$ele['value'].'" placeholder="'.$ele['placeholder'].'" ';
        if (isset($ele['required'])){
            $html .= ' required="" ';
        }
        if (isset($ele['readonly'])){
            $html .= ' readonly="" ';
        }
        $html .= ">";
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    private function buildSubmit($ele){

        $html = '<div class="form-group '.$ele['type'].'">';
        $html .= '<input type="'.$ele['type'].'" value="'.$ele['value'].'" >';
        $html .= '</div>';

        return $html;
    }

    /**
     * 设置action
     * @param $action
     * @return $this
     */
    public function setAction($action){

        $config = $this->getConfig();
        $config['action'] = $action;

        $this->action = $action;
        $this->setConfig($config);

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
            $config = $this->getConfig();
            $config['method'] = $method;

            $this->method = $method;
            $this->setConfig($config);
        }

        return $this;
    }

    /**
     * 设置表单上传文件
     * @return $this
     */
    public function uploadFile(){

        $config = $this->getConfig();
        $config['upload_file'] = true;
        $this->setConfig($config);

        $this->enctype = 'multipart/form-data';

        return $this;
    }

    public function addElement($element){

        $elements = $this->formElements;
        $elements[] = $element;

        $this->formElements = $elements;

        return $this;
    }

    public function getElements(){

        $elements = $this->formElements;
        return $elements;
    }
    /**
     * 添加隐藏元素
     * @param $name
     * @param string $value
     * @param string $placeholder
     * @param bool $required
     * @param bool $readonly
     */
    public function addHidden($name , $title = '',$value = '' , $placeholder = '' , $required = false , $readonly = false){

        $this->addInput('hidden' , $name ,$title , $value  , $placeholder  , $required  , $readonly );

        return $this;

    }

    /**
     * 添加text
     * @param $name
     * @param string $value
     * @param string $placeholder
     * @param bool $required
     * @param bool $readonly
     * @return $this
     */
    public function addText($name , $title = '',$value = '' , $placeholder = '' , $required = false , $readonly = false){

        $this->addInput('text' , $name , $title ,$value  , $placeholder  , $required  , $readonly );
        return $this;
    }

    public function addPassword($name , $title = '',$value = '' , $placeholder = '' , $required = false , $readonly = false){

        $this->addInput('password' , $name , $title ,$value  , $placeholder  , $required  , $readonly );
        return $this;
    }

    public function addEmail($name , $title = '',$value = '' , $placeholder = '' , $required = false , $readonly = false){

        $this->addInput('email' , $name , $title ,$value  , $placeholder  , $required  , $readonly );
        return $this;
    }

    public function addNumber($name , $title = '',$value = '' , $placeholder = '' , $required = false , $readonly = false){

        $this->addInput('number' , $name , $title ,$value  , $placeholder  , $required  , $readonly );
        return $this;
    }

    public function addFile($name , $title = '',$value = '' , $placeholder = '' , $required = false , $readonly = false){
        $this->addInput('file' , $name , $title ,$value  , $placeholder  , $required  , $readonly );
        return $this;
    }

    public function addImage($name , $title = '',$value = '' , $placeholder = '' , $required = false , $readonly = false){
        $this->addInput('image' , $name , $title ,$value  , $placeholder  , $required  , $readonly );
        return $this;
    }

    public function addButton($name , $title = '',$value = '' , $placeholder = '' , $required = false , $readonly = false){
        $this->addInput('button' , $name , $title ,$value  , $placeholder  , $required  , $readonly );
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
    public function addInput($type = 'text' ,$name , $title ='' ,$value = '' , $placeholder = '' , $required = false , $readonly = false){

        $data = [

            'type' => $type,
            'name' => $name,
            'title' => $title,
            'id'  => $this->formName . '-' . $name ,
            'placeholder' => $placeholder,
            'value' => $value,
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

    public function addTextarea($name , $title ='' ,$value = '' , $placeholder = '' , $required = false , $readonly = false) {

        $data = [

            'type' => 'textarea',
            'name' => $name,
            'title' => $title,
            'id'  => $this->formName . '-' . $name ,
            'placeholder' => $placeholder,
            'value' => $value,
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
    public function addSelect($name , $title ='' ,$value = '' , $extra = [] , $required = false , $readonly = false){

        $this->addChoose('select' , $name , $title ,$value , $extra , $required , $readonly );
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
    public function addRadio($name , $title ='' ,$value = '' , $extra = [] , $required = false , $readonly = false){
        $this->addChoose('radio' , $name , $title ,$value , $extra , $required , $readonly );
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
    public function addCheckbox($name , $title ='' ,$value = '' , $extra = [] , $required = false , $readonly = false){
        $this->addChoose('radio' , $name , $title ,$value , $extra , $required , $readonly );
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
    public function addChoose($type , $name , $title ='' ,$value = '' , $extra = [] , $required = false , $readonly = false){

        $data = [

            'type' => $type,
            'name' => $name,
            'title' => $title,
            'id'  => $this->formName . '-' . $name ,
            'extra' => $extra,
            'value' => $value,
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