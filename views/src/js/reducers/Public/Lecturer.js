import * as types from 'Constants'

const initialState = {
    data: {},
    areas: []
}

export default (state = initialState, action) => {
    switch (action.type) {
        case `${types.LOAD_PUBLIC_LECTURER}_FULFILLED`:
            return {
                ...state,
                data: action.payload.data
            }
        case `${types.LOAD_PUBLIC_LECTURER_AREAS}_FULFILLED`:
            return {
                ...state,
                areas: action.payload.data.data
            }
        default:
            return state
    }
}
