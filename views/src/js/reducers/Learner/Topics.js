import * as types from 'Constants'

const initialState = {
    isLoaded: false,
    data: null
}

export default (state = initialState, action) => {
    switch (action.type) {
        case `${types.LEARNER_LOAD_TOPICS}_FULFILLED`:
            return {
                ...state,
                isLoaded: true,
                data: action.payload.data.data
            }
        default:
            return state
    }
}
