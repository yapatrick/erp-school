import { messageConstants } from '../_constants/message.constants';
export const messageActions = {
    setMessage,
    clearMessage
};

//Function setMessage
function setMessage(message){
    return {
        type: messageConstants.SET_MESSAGE,
        payload: message,
    };
};

//Function clearMessage
function clearMessage(){
    return {
        type: messageConstants.CLEAR_MESSAGE
    };
  }