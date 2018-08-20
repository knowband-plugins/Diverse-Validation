<?php

/**
 * Dashboard Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Velocity Software Solutions (http://www.velsof.com)
 * @link          http://www.mockingfish.com MockingFish Project
 * @since         3 Feb 2015
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 * @version		 1.0
 * @todo
 */
class ValidationController extends AppController {

    public $uses = array('Rule');

    function beforeFilter() {
        $this->Auth->allow('index', 'api', 'parser');
    }

    public function index($content = '') {
        $error = false;
        $outputTags = array();
        if ($this->request->is("post")) {
            if (isset($this->data["Validation"]["content"]) && $this->data["Validation"]["content"] != "") {
                $outputTags = $this->parser($this->data["Validation"]["content"]);
                if (is_array($outputTags)) {
                    $this->set("outputTags", $outputTags);
                } else {
                    $this->set("notagfound", $outputTags);
                }
            } else {
                $error = true;
            }
        }
        if ($error == true) {
            $this->set("emptyfield", "Please enter the HTML Content.");
        } else {
            $this->set("emptyfield", "");
        }
        @$this->data["Validation"]["content"] = "";
        $this->set("subtitle", "Validation Parser");
        $this->render("index");
    }

    function api() {
        header("Content-Type: application/json");
        $this->layout = false;
        if(isset($this->data['content']) && trim($this->data['content']) != ''){
            $output = $this->parser($this->data['content']);
    //        $rules["email"] = array("rules" => array("data-rule-email" => true));
            echo json_encode($output);
        }
        die();
    }

    protected function parser($content) {
        App::import('Vendor', 'simple_html_dom', array('file' => 'htmldom/simple_html_dom.php'));
        $html = str_get_html($content);
        $parsedTags = $html->find('input,select,radio,checkbox,textarea');
        if (count($parsedTags) > 0) {
            $rules = $this->Rule->find("all");

            $ruleArray = array();
            foreach ($rules as $rule) {
                $ruleArray[strtolower($rule["Rule"]["rule_key"])] = array("message" => $rule["Rule"]["rule_message"], "description" => $rule["Rule"]["rule_details"]);
            }
            //print_r($ruleArray);
            foreach ($parsedTags as $tags) {
                $output = array();
                if ($tags->getAttribute("type") == 'text' || $tags->getAttribute("type") == "password" || $tags->getAttribute("type") == "checbox" || $tags->getAttribute("type") == "radio" || $tags->tag == "textarea" || $tags->tag == "select" || $tags->getAttribute("type") == "file") {
                    $output["name"] = $tags->getAttribute("name");
                    if ($tags->getAttribute("type") != "") {
                        $output["type"] = $tags->getAttribute("type");
                    } else {
                        $output["type"] = $tags->tag;
                    }
                    $rules = array();

                    foreach ($tags->getAllAttributes() as $key => $currenttag) {
                        if (preg_match('#^data-rule-#i', $key) === 1) {

                            $rule = str_replace("data-rule-", "", $key);
                            $ruleType = "Custom";
                            $message = "";
                            $description = "";
                            if (array_key_exists(strtolower(trim($rule)), $ruleArray) === true) {
                                $ruleType = "Standard";
                                $message = $ruleArray[strtolower(trim($rule))]['message'];
                                $description = $ruleArray[strtolower(trim($rule))]['description'];
                            }

                            foreach ($tags->getAllAttributes() as $messagekey => $currenttag1) {
                                if ($messagekey == 'data-msg-' . $rule) {
                                    $message = $currenttag1;
                                }
                            }

                            $rules[] = array("rule" => trim($rule), "tag" => trim($currenttag), "ruleType" => $ruleType, "message" => $message, "description" => $description);
                        }
                    }
                    $output["rules"] = $rules;
                    $outputTags[] = $output;
                }
            }
            return $outputTags;
        } else {
            return "Sorry! No form fields found on the HTML.";
        }
    }
}

?>