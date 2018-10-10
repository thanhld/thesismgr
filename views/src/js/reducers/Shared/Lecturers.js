import * as types from 'Constants'
import { sortOfficers } from 'Helper'

const initialState = {
    isLoaded: false,
    list: []
}

export default (state = initialState, action) => {
    switch (action.type) {
        case `${types.LOAD_LECTURERS}_FULFILLED`:
            return {
                ...state,
                isLoaded: true,
                list: action.payload.data.data.sort(sortOfficers)
            }
        default:
            return state
    }
}
