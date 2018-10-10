import * as types from 'Constants'

const initialState = {
    isLoaded: false,
    list: []
}

export default (state = initialState, action) => {
    switch (action.type) {
        case `${types.LOAD_TRAINING_LEVELS}_FULFILLED`:
            return {
                ...state,
                isLoaded: true,
                list: action.payload.data.data
            }
        default:
            return state
    }
}
