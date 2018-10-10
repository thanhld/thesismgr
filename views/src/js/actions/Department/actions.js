import axios from 'axios'
import * as types from 'Constants'

export const flushTopics = () => {
    return {
        type: types.DEPARTMENT_FLUSH_TOPICS
    }
}
