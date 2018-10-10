import * as types from 'Constants'

const initialState = {
    isLoaded: false,
    list: []
}

export default (state = initialState, action) => {
    switch (action.type) {
        case `${types.LECTURER_LOAD_TOPICS}_FULFILLED`:
            const { mainTopic, topicChange } = action.payload.data
            const listTopics = [...mainTopic.data]
            topicChange.data.forEach(tc => {
                const alreadyInList = mainTopic.data.find(main => main.id == tc.id)
                if (!alreadyInList) listTopics.push(tc)
            })
            return {
                ...state,
                isLoaded: true,
                list: listTopics
            }
        default:
            return state
    }
}
