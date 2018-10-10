import * as types from 'Constants'

const initialState = {
    isLoaded: false
}

export default (state = initialState, action) => {
    switch (action.type) {
        case `${types.SUPERUSER_LOAD_FACULTIES}_FULFILLED`:
            return {
                ...state,
                isLoaded: true,
                list: action.payload.data.data
            }
        default:
            return state
    }
}
