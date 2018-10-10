import { combineReducers } from 'redux'
import { routerReducer as routing } from 'react-router-redux'
import { loadingBarReducer } from 'react-redux-loading-bar'
import { LOGOUT } from 'Constants'

import auth from './auth'
// Store
import degrees from './Shared/Degrees'
import trainingTypes from './Shared/TrainingTypes'
import trainingLevels from './Shared/TrainingLevels'
import departments from './Shared/Departments'
import knowledgeAreas from './Shared/KnowledgeAreas'
import announcements from './Shared/Announcements'
import lecturers from './Shared/Lecturers'
import outOfficers from './Shared/OutOfficers'
import quotas from './Shared/Quotas'
// Superuser store
import superuserFaculties from './Superuser/Faculties'
import superuserQuotas from './Superuser/Quotas'
// Admin store
import adminOfficers from './Admin/Officers'
import adminDepartments from './Admin/Departments'
import adminTrainingAreas from './Shared/TrainingAreas'
import adminTrainingCourses from './Admin/TrainingCourses'
import adminTrainingPrograms from './Shared/TrainingPrograms'
import adminLearners from './Admin/Learners'
import adminTopics from './Admin/Topics'
import adminOutTopics from './Admin/OutTopics'
import adminDocuments from './Admin/Documents'
// Lecturer store
import lecturerProfile from './Lecturer/Profile'
import lecturerTopics from './Lecturer/Topics'
// Learner store
import learnerProfile from './Learner/Profile'
import learnerTopics from './Learner/Topics'
// Department store
import departmentTopicExpertise from './Department/TopicExpertise'
import departmentSeminar from './Department/Seminar'
// Public store
import browseLecturers from './Public/BrowseLecturers'
import publicLecturer from './Public/Lecturer'

const appReducer = combineReducers({
    auth,
    degrees,
    trainingTypes,
    trainingLevels,
    departments,
    knowledgeAreas,
    announcements,
    lecturers,
    outOfficers,
    quotas,
    superuserFaculties,
    superuserQuotas,
    adminOfficers,
    adminDepartments,
    adminTrainingAreas,
    adminTrainingPrograms,
    adminTrainingCourses,
    adminLearners,
    adminTopics,
    adminOutTopics,
    adminDocuments,
    lecturerProfile,
    lecturerTopics,
    learnerProfile,
    learnerTopics,
    departmentTopicExpertise,
    departmentSeminar,
    browseLecturers,
    publicLecturer,
    loadingBar: loadingBarReducer,
    routing
})

export const rootReducer = (state, action) => {
    if (action.type == `${LOGOUT}_FULFILLED`)
        state = undefined
    return appReducer(state, action)
}
