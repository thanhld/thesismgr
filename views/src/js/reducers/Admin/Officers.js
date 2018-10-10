import * as types from 'Constants'
import { sortOfficers } from 'Helper'

const initialState = {
    isLoaded: false,
    list: []
}

export default (state = initialState, action) => {
    switch (action.type) {
        case `${types.ADMIN_LOAD_OFFICERS}_FULFILLED`:
            return {
                ...state,
                isLoaded: true,
                list: action.payload.data.data.sort(sortOfficers)
            }
        default:
            return state
    }
}
