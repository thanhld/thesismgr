import * as types from 'Constants'

const initialState = {
    isLoaded: false,
    areas: []
}

export default (state = initialState, action) => {
    switch (action.type) {
        case `${types.LOAD_AREAS}_FULFILLED`:
            return {
                ...state,
                isLoaded: true,
                areas: action.payload.data.data
            }
        default:
            return state
    }
}
