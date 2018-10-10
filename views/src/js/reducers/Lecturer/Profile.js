import * as types from 'Constants'

const initialState = {
    isLoaded: false,
    lecturer: {},
    addedAreas: []
}

export default (state = initialState, action) => {
    switch (action.type) {
        case `${types.LECTURER_LOAD_INFOMATION}_FULFILLED`:
            return {
                ...state,
                isLoaded: true,
                lecturer: action.payload.data
            }
        case `${types.LECTURER_LOAD_AREAS}_FULFILLED`:
            return {
                ...state,
                addedAreas: action.payload.data.data
            }
        default:
            return state
    }
}
