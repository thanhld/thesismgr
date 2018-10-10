import * as types from 'Constants'

const initialState = {
    isLoaded: false,
    list: []
}

export default (state = initialState, action) => {
    switch (action.type) {
        case `${types.LOAD_ANNOUNCEMENTS}_FULFILLED`:
            return {
                ...state,
                isLoaded: true,
                list: action.payload.data.data
            }
        case `${types.ADMIN_LOAD_ANNOUNCEMENTS}_FULFILLED`:
            return {
                ...state,
                isLoaded: true,
                list: action.payload.data.data
            }
        default:
            return state
    }
}
