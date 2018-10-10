import * as types from 'Constants'
import { sortDepartments } from 'Helper'

const initialState = {
    isLoaded: false,
    list: []
}

export default (state = initialState, action) => {
    switch (action.type) {
        case `${types.LOAD_DEPARTMENTS}_FULFILLED`:
            return {
                ...state,
                isLoaded: true,
                list: action.payload.data.data.sort(sortDepartments)
            }
        default:
            return state
    }
}
