import * as types from 'Constants'
import { sortTopics } from 'Helper'

const initialState = {
    isLoaded: false,
    list: []
}

export default (state = initialState, action) => {
    switch (action.type) {
        case `${types.ADMIN_LOAD_TOPICS}_FULFILLED`:
            return {
                ...state,
                isLoaded: true,
                list: action.payload.data.data.sort(sortTopics)
            }
        case types.ADMIN_FLUSH_TOPICS:
            return {
                ...state,
                isLoaded: false,
                list: []
            }
        default:
            return state
    }
}
