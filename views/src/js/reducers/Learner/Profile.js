import * as types from 'Constants'

const initialState = {
    isLoaded: false,
    data: {}
}

export default (state = initialState, action) => {
    switch (action.type) {
        case `${types.LEARNER_LOAD_PROFILE}_FULFILLED`:
            return {
                ...state,
                isLoaded: true,
                data: action.payload.data
            }
        default:
            return state
    }
}
