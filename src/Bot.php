<?php

namespace ArashAbedii;

class Bot {

    //VARIABLES
    private $update;
    private $token;
    private $chat_id;
    private $text;
    private $username;
    private $firstname;
    private $message_id;
    private $type;
    private $date;
    private $callback_qyery_id;
    private $callback_qyery_data;
    private $language_code;
    private $callbackQueryId;
    private $callbackQueryData;
    private $inlineQueryId;
    private $inlineQueryQuery;
    private $inlineQueryOffset;
    private $sentMessage_id;


    //SETUP BOT CLASS
    public function __construct($token)
    {
        $this->token=$token;
        $this->update=json_decode(file_get_contents('php://input'),true);
        $this->setChat_id();
        $this->setText();
        $this->setMessage_id();
        $this->setUserName();
        $this->setFirstName();
        $this->setType();
        //set callback query
        if($this->getUpdateType()=='callback_query'){
            $this->setCallbackQueryId();
            $this->setCallbackQueryData();
        }elseif($this->getUpdateType()=='inline_query'){
            $this->setInlineQueryId();
            $this->setInlineQueryData();
            $this->setInlineQueryOffset();
        }

        
    }


    //GENERAL REQUEST 
    public function bot($methodName,array $params){
        $request_url="https://api.telegram.org/bot$this->token/$methodName";
        $response=static::request($request_url,$params,'POST');
        
        if(isset(json_decode($response)->result->message_id)){
            $this->setBotSentMessagesMessage_id(json_decode($response)->result->message_id);
        }
        
        return $response;
    }

    public static function sendCustomRequest($token,$methodName,$params){
        $request_url="https://api.telegram.org/bot".$token."/$methodName";
        $response=static::request($request_url,$params,'POST');
        return $response;
    }


    //SET WEBHOOK
    public static function setWebHook($token,array $params){
        $result=static::request("https://api.telegram.org/bot$token/setWebhook",$params,'get');
        var_dump($result);
    }

    //DELETE WEBHOOK
    public static function deleteWebHook($token,array $params){
        $result=static::request("https://api.telegram.org/bot$token/deleteWebhook",$params,'get');
        var_dump($result);
    }

    private function request($url,$data){
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        $result=curl_exec($ch);
        curl_close($ch);
        return $result;
    }
  
    //SEND MESSAGES
    public function sendMessage(array $params){
        //insert chat_id to params
        $params['chat_id']=isset($params['chat_id']) ? $params['chat_id'] : $this->chat_id;

       //send message
        return $this->bot('sendMessage',$params);
        
    }

    //FORWARD MESSAGE
    public function forwardMessage(array $params){
        //insert chat_id to params
        $params['chat_id']=isset($params['chat_id']) ? $params['chat_id'] : $this->chat_id;

       //send message
        return $this->bot('forwardMessage',$params);
        
    }

    //DELETE MESSAGE
    public function deleteMessage($message_id=""){
        //delete message
        if($message_id==""){
            $message_id=$this->message_id;
        }
        return $this->bot('deleteMessage',['chat_id'=>$this->chat_id,'message_id'=>$message_id]);
    }

    //EDIT A MESSAGE
    public function editTextMessage(){

    }

    //edit reply markup
    public function editReplyMarkupMessage($replyMarkup,$message_id=""){
        if($message_id==""){
            $message_id=$this->message_id;
        }
        
        return $this->bot("editMessageReplyMarkup",['chat_id'=>$this->chat_id,'message_id'=>$message_id,'inline_message_id'=>$this->getCallbackQueryId(),'reply_markup'=>$replyMarkup]);
    }



    //GET UPDATE TYPE
    public function getUpdateType(){
        if(isset($this->update['message'])){
            return 'message';
        }elseif(isset($this->update['my_chat_member'])){
            return "my_chat_member";
        }elseif(isset($this->update['edited_message'])){
            return 'edited_message';
        }elseif(isset($this->update['channel_post'])){
            return 'channel_post';
        }elseif(isset($this->update['edited_channel_post'])){
            return 'edited_channel_post';
        }elseif(isset($this->update['inline_query'])){
            return 'inline_query';
        }elseif(isset($this->update['callback_query'])){
            return 'callback_query';
        }elseif(isset($this->update['poll'])){
            return 'poll';
        }elseif(isset($this->update['poll_answer'])){
            return 'poll_answer';
        }elseif(isset($this->update['my_chat_member'])){
            return 'my_chat_member';
        }elseif(isset($this->update['chat_member'])){
            return 'chat_member';
        }
    }

    //GET UPDATE 
    public function getUpdate(){
        return $this->update;
    }


    //SEQURITY OPTION
    //this function , ignore invalid requests , It just access to telegram requests
    public static function requestsHandler(){
        $update=json_decode(file_get_contents("php://input"));
        $status=isset($update->update_id) ? true : false;
        return $status;
    }

    //SET CHAT_ID
    private function setChat_id(){
        if($this->getUpdateType()=='message'){
            $this->chat_id=$this->update['message']['chat']['id'];
        }elseif($this->getUpdateType()=='my_chat_member'){
            $this->chat_id=$this->update['my_chat_member']['chat']['id'];
        }elseif($this->getUpdateType()=='callback_query'){
            $this->chat_id=$this->update['callback_query']['from']['id'];
        }elseif($this->getUpdateType()=='channel_post'){
            $this->chat_id=$this->update['channel_post']['chat']['id'];
        }elseif($this->getUpdateType()=='inline_query'){
            $this->chat_id=$this->update['inline_query']['from']['id'];
        }elseif($this->getUpdateType()=='edited_message'){
            $this->chat_id=$this->update['edited_message']['from']['id'];
        }elseif($this->getUpdateType()=='edited_channel_post'){
            $this->chat_id=$this->update['edited_channel_post']['chat']['id'];
        }elseif($this->getUpdateType()=='edited_message'){
            $this->chat_id=$this->update['edited_message']['from']['id'];
        }
    }
    //GET CHAT_ID
    public function getChat_id(){
        return $this->chat_id;
    }

    //SET CHAT TYPE
    public function setType(){
        if(isset($this->update['message'])){
            $this->type=$this->update['message']['chat']['type'];
        }elseif(isset($this->update['inline_query'])){
            $this->type=!empty($this->update['inline_query']['chat_type']) ? $this->update['inline_query']['chat_type'] : null;
        }elseif(isset($this->update['callback_query'])){
            $this->type=isset($this->update['callback_query']['message']['chat']['type']) ? $this->update['callback_query']['message']['chat']['type'] : null;
        }
    }

    //GET TYPE
    public function getType(){
        return $this->type;
    }

    //USER DATA
    public function setFirstName(){
        if($this->getUpdateType()=='message'){
            $this->firstname=$this->update['message']['from']['first_name'];
        }elseif($this->getUpdateType()=='callback_query'){
            $this->firstname=$this->update['callback_query']['from']['first_name'];
        }elseif($this->getUpdateType()=='inline_query'){
            $this->firstname=$this->update['inline_query']['from']['first_name'];
        }
    }

    public function getUserName(){
        return $this->username;
    }


    public function setUserName(){
        if($this->getUpdateType()=='message'){
            $this->username=isset($this->update['message']['from']['username']) ? $this->update['message']['from']['username'] : "Not Found username" ;
        }elseif($this->getUpdateType()=='callback_query'){
            $this->username=isset($this->update['callback_query']['from']['username']) ? $this->update['callback_query']['from']['username'] : "Not Found username";
        }elseif($this->getUpdateType()=='inline_query'){
            $this->username=isset($this->update['inline_query']['from']['username']) ? $this->update['inline_query']['from']['username'] : "Not Found username";
        }
    }

    public function getFirstName(){
        return $this->firstname;
    }


    //SET TEXT 
    private function setText(){
        if($this->getUpdateType()=='message'){
            $this->text=isset($this->update['message']['text']) ? $this->update['message']['text'] : "";
        }elseif($this->getUpdateType()=='callback_query'){
            $this->text=isset($this->update['callback_query']['message']['text']) ? $this->update['callback_query']['message']['text'] : null;
        }elseif($this->getUpdateType()=='channel_post'){
           // $this->text=$this->update['channel_post']['message']['text'];
        }elseif($this->getUpdateType()=='inline_query'){
            $this->text=isset($this->update['inline_query']['query']) ? $this->update['inline_query']['query'] : null;
        }
    }

    //CALLBACK QUERY
    private function setCallbackQueryId(){
        if(isset($this->update['callback_query']['id'])){
            $this->callbackQueryId=$this->update['callback_query']['id'];
        }
    }
    private function setCallbackQueryData(){
        if(isset($this->update['callback_query']['data'])){
            $this->callbackQueryData=$this->update['callback_query']['data'];
        }
    }

    public function getCallbackQueryData(){
        return $this->callbackQueryData;
    }

    public function getCallbackQueryId(){
        return $this->callbackQueryId;
    }

    //INLINE QUERY
    private function setInlineQueryId(){
        if(isset($this->update['inline_query']['id'])){
            $this->inlineQueryId=$this->update['inline_query']['id'];
        }
    }

    public function getInlineQueryId(){
        return $this->inlineQueryId;
    }

    private function setInlineQueryData(){
        if(isset($this->update['inline_query']['query'])){
            $this->inlineQueryQuery=$this->update['inline_query']['query'];
        }
    }

    public function getInlineQueryData(){
        return $this->inlineQueryQuery;
    }

    public function setInlineQueryOffset(){
        if(isset($this->update['inline_query']['offset'])){
            $this->inlineQueryOffset=$this->update['inline_query']['offset'];
        }
    }

    public function getInlineQueryOffset(){
        return $this->inlineQueryOffset;
    }
    
    
    //GET TEXT
    public function getText(){
        return $this->text;
    }

    //SET MESSAGE ID
    private function setMessage_id(){
        if($this->getUpdateType()=='message'){
            $this->message_id=$this->update['message']['message_id'];
        }elseif($this->getUpdateType()=='callback_query'){
            $this->message_id=$this->update['callback_query']['message']['message_id'];
        }elseif($this->getUpdateType()=='channel_post'){
            //channel post
        }
    }

    //GET MESSAGE ID
    public function getMessage_id(){
        return $this->message_id;
    }

    //SET BOT SENT MESSAGES MESSAGE_ID
    public function setBotSentMessagesMessage_id($message_id){
        $this->sentMessage_id=$message_id;
    }

    //GET BOT SENT MESSAGES MESSAGE_ID
    public function getBotSentMessagesMessage_id(){
        return $this->sentMessage_id;
    }


    //UPDATE MESSAGE ID
    public function updateMessage_id($value){
        $this->message_id=$value;
    }



    //MULTI PROCCESS
    public static function multiProccess($mainBotFileUrl){
        //code
        $inputs=file_get_contents('php://input');
        
        $ch=curl_init();
        curl_setopt($ch,CURLOPT_URL,$mainBotFileUrl);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$inputs);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,false);
        curl_setopt($ch,CURLOPT_TIMEOUT,1);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,1);
        curl_exec($ch);
        curl_close($ch);
        
    }

    //BOT REPAIR HANDLER
    public static function repairHandler($message='bot is repairing.try again later'){
        $bot=new Bot(TOKEN);
        $bot->sendMessage(['text'=>$message]);
        die();
    }

    //SEND BOT ACTION
    public function sendChatAction(string $action){
        return $this->bot('sendChatAction',['chat_id'=>$this->chat_id,'action'=>$action]);
    }


    //MEDIA

    //audios
    public function sendAudio($params){
        $params['chat_id']=isset($params['chat_id']) ? $params['chat_id'] : $this->chat_id;
        $result=$this->bot('sendAudio',$params);
        return $result;
    }

    public function getAudio(){
        if(isset($this->update['message']['audio'])){
            return $this->update['message']['audio'];
        }elseif(isset($this->update['channel_post']['audio'])){
             return $this->update['channel_post']['audio'];
        }
    }

    public function getAudioFileId(){
        if(isset($this->update['message']['audio'])){
            return $this->update['message']['audio']['file_id'];
        }elseif(isset($this->update['channel_post']['audio'])){
             return $this->update['channel_post']['audio']['file_id'];
        }
    }

    public function getAudioPerformer(){
        if(isset($this->update['message']['audio'])){
            return $this->update['message']['audio']['performer'];
        }elseif(isset($this->update['channel_post']['audio'])){
             return $this->update['channel_post']['audio']['performer'];
        }
    }

    public function getAudioCaption(){
        if(isset($this->update['message']['audio'])){
            return $this->update['message']['caption'];
        }elseif(isset($this->update['channel_post']['audio']['caption'])){
             return $this->update['channel_post']['caption'];
        }
        return false;
    }

    public function getAudioDuration(){
        if(isset($this->update['message']['audio'])){
            return $this->update['message']['audio']['duration'];
        }elseif(isset($this->update['channel_post']['audio']['duration'])){
             return $this->update['channel_post']['audio']['duration'];
        }
    }

    public function getAudioTitle(){
        if(isset($this->update['message']['audio'])){
            return $this->update['message']['audio']['title'];
        }elseif(isset($this->update['channel_post']['audio'])){
             return $this->update['channel_post']['audio']['title'];
        }
    }

    public function getAudioFileName(){
        if(isset($this->update['message']['audio'])){
            return $this->update['message']['audio']['file_name'];
        }elseif(isset($this->update['channel_post']['audio'])){
             return $this->update['channel_post']['audio']['file_name'];
        }
    }

    public function getAudioThumbnail(){
        if(isset($this->update['message']['audio']['thumb'])){
            return $this->update['message']['audio']['thumb']['file_id'];
        }elseif(isset($this->update['channel_post']['audio']['thumb'])){
             return $this->update['channel_post']['audio']['thumb']['file_id'];
        }
        return false;
    }

    public function getAudioFileSize(){
        if(isset($this->update['message']['audio']['file_size'])){
            return $this->update['message']['audio']['file_size'];
        }elseif(isset($this->update['channel_post']['audio']['file_size'])){
             return $this->update['channel_post']['audio']['file_size'];
        }
        return false;
    }

    //photos
    public function sendPhoto($params){
        $params['chat_id']=isset($params['chat_id']) ? $params['chat_id'] : $this->chat_id;
        $result=$this->bot('sendPhoto',$params);
        return $result;
    }

    public function getPhotoFileId(){
        if(isset($this->update['message']['photo'])){
            if(isset($this->update['message']['photo'][2])){
                return $this->update['message']['photo'][2]['file_id'];
            }
            elseif(isset($this->update['message']['photo'][1])){
                return $this->update['message']['photo'][1]['file_id'];
            }else{
                return $this->update['message']['photo'][0]['file_id'];
            }
            
        }
    }


    //DOWNLOAD AND UPLOAD FILES
    public function getFile($fileId){

        //get file data from telegram
        $fileData=json_decode($this->bot('getFile',['file_id'=>$fileId]));
        if($fileData->ok==true){
            return $fileData;
        }else{
            return false;
        }
        
    }

    public function getFilePath($fileId){

        //get file path
        $fileData=$this->getFile($fileId);
        if($fileData){
            return $fileData->result->file_path;
        }else{
            return false;
        }
        
    }

    public function getFileDownloadLink($fileId){
        $path=$this->getFilePath($fileId);
        if($path){
            $link="https://api.telegram.org/file/bot".TOKEN."/".$path;
            return $link;
        }else{
            return false;
        }
    }

    //KEYBOARDS  PATERN LIKE: "$btn1--$btn2--$btn3//$btn4--$btn6//$btn7--$btn8"
    public function setKeyboardButtons(string $patern,array $options=['resize_keyboard'=>true,'one_time_keyboard'=>false]){
        $rows=explode("//",$patern);
        $index=0;
        foreach($rows as $row){
            $cols[$index]=explode("--",$row);
            foreach($cols[$index] as $key=>$value){
                if(strpos($value,"::")!==false){
                    $as=explode("::",$value);
                    $cols[$index][$key]=[
                        'text'=>$as[0],
                        'request_contact'=>isset($as[1]) && $as[1]=="request_contact" ? true : false,
                        'request_location'=>isset($as[1]) && $as[1]=="request_location" ? true : false,
                    ];
                }
            }
            $index++;
        }
        $options['keyboard']=$cols;
        
        return json_encode($options);
    }

    //create inline keyboard * pass array of buttons to this function
    /*$buttons=[
        [
            ['text'=>'a','url'=>'https://google.com'];
        ]
    ]*/
    public function setInlineKeyboard(array $buttons){
        return json_encode(['inline_keyboard'=>$buttons]);
    }

    public function answerCallbackQuery(array $params=[]){
        if($params==[]){
            $params=['callback_query_id'=>$this->getCallbackQueryId(),'cache_time'=>1];
        }elseif(!isset($params['callback_query_id'])){
            $params['callback_query_id']=$this->getCallbackQueryId();
        }
        $this->bot('answerCallbackQuery',$params);
    }

    public function answerInlineQuery(array $params){
        return $this->bot("answerInlineQuery",$params);
    }
}