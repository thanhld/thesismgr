import * as types from 'Constants'
import { sortTopics } from 'Helper'

const initialState = {
    isLoaded: false,
    list: []
}

export default (state = initialState, action) => {
    switch (action.type) {
        case `${types.ADMIN_LOAD_OUT_TOPICS}_FULFILLED`:
            return {
                ...state,
                isLoaded: true,
                list: action.payload.data.data.sort(sortTopics)
            }
        default:
            return state
    }
}
