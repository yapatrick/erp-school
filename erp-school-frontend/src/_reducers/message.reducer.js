import { messageConstants } from "../_constants/message.constants";

const initialState = {};

function message (state = initialState, action){
  const { type, payload } = action;

  switch (type) {
    case messageConstants.SET_MESSAGE:
      return { message: payload };

    case messageConstants.CLEAR_MESSAGE:
      return { message: "" };

    default:
      return state;
  }
}

export { message };