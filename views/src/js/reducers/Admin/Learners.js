import * as types from 'Constants'
import { sortLearners } from 'Helper'

const initialState = {
    isLoaded: false,
    count: 0,
    list: []
}

export default (state = initialState, action) => {
    switch (action.type) {
        case `${types.ADMIN_LOAD_LEARNERS}_FULFILLED`:
            return {
                ...state,
                count: action.payload.data.count,
                list: action.payload.data.data.sort(sortLearners)
            }
        default:
            return state
    }
}
