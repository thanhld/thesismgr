import * as types from 'Constants'

const initialState = {
    isLoaded: false,
    list: []
}

export default (state = initialState, action) => {
    switch (action.type) {
        case `${types.DEPARTMENT_LOAD_TOPICS}_FULFILLED`:
            return {
                ...state,
                isLoaded: true,
                list: action.payload.data.data
            }
        case types.DEPARTMENT_FLUSH_TOPICS:
            return {
                ...state,
                isLoaded: true,
                list: []
            }
        default:
            return state
    }
}
