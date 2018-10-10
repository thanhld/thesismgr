import * as types from 'Constants'

const initialState = {
    isLoaded: false,
    list: []
}

export default (state = initialState, action) => {
    switch (action.type) {
        case `${types.ADMIN_LOAD_DOCUMENTS}_FULFILLED`:
            return {
                ...state,
                isLoaded: true,
                list: action.payload.data.data
            }
        default:
            return state
    }
}
