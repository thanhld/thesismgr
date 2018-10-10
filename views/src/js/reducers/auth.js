import * as types from 'Constants'

const initialState = {
    isLoaded: false
}

export default (state = initialState, action) => {
    switch (action.type) {
        case `${types.AUTH}_FULFILLED`:
            return {
                ...state,
                isLoaded: true,
                user: action.payload.data
            }
        case `${types.AUTH}_REJECTED`:
            return {
                isLoaded: false
            }
        case `${types.AUTH_LOAD}_FULFILLED`:
            return {
                ...state,
                isLoaded: true,
                user: action.payload.data
            }
        case `${types.AUTH_LOAD}_REJECTED`:
            return {
                isLoaded: false
            }
        case `${types.LOGOUT}_FULFILLED`:
            return {
                isLoaded: false
            }
        default:
            return state
    }
}
