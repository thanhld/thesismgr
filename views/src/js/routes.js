import React from 'react'
import { IndexRedirect, IndexRoute, Route } from 'react-router'
import { authActions } from 'Actions'
import { browserHistory } from 'react-router'
import { SUPERUSER_ROLE, ADMIN_ROLE } from 'Constants'
import { routeName } from 'Config'
import * as helper from 'Helper'
import {
    NotFound
} from 'Components'
import {
    Layout,
    Login,
    SetPassword,
    SuperuserDegrees,
    SuperuserFaculties,
    SuperuserTrainings,
    SuperuserQuotas,
    AdminDepartments,
    AdminOfficers,
    AdminTrainingAreas,
    AdminTrainingPrograms,
    AdminTrainingCourses,
    AdminTrainingTypesAndLevels,
    AdminDegrees,
    AdminKnowledgeAreas,
    AdminLearners,
    AdminTopics,
    AdminOutOfficers,
    AdminDocuments,
    LecturerProfile,
    LecturerCurrentTopics,
    LecturerExpertise,
    LearnerProfile,
    LearnerTopics,
    DepartmentTopicExpertise,
    DepartmentSeminar,
    TopicActivity,
    PublicLecturer,
    BrowseLecturers,
    Home,
} from 'Containers'

export default (store) => {
    const requireLogin = (nextState, replace, cb) => {
        const { auth: { isLoaded }} = store.getState()
        if (!isLoaded) {
            store.dispatch(authActions.loadAuth()).then(() => {
                const { auth: { user }} = store.getState()
                if (!user) browserHistory.push(`/${routeName['LOGIN']}`)
                cb()
            }).catch(err => {
                browserHistory.push(`/${routeName['LOGIN']}`)
                cb()
            })
        } else cb()
    }
    const requireSuperuser = (nextState, replace, cb) => {
        const { user } = store.getState().auth
        if (!helper.isSuperuser(user)) replace('/')
        cb()
    }
    const requireOfficerAdmin = (nextState, replace, cb) => {
        const { user } = store.getState().auth
        if (!helper.isOfficerAdmin(user)) replace('/')
        cb()
    }
    const requireLecturer = (nextState, replace, cb) => {
        const { user } = store.getState().auth
        if (!helper.isLecturer(user)) replace('/')
        cb()
    }
    const requireLearner = (nextState, replace, cb) => {
        const { user } = store.getState().auth
        if (!helper.isLearner(user)) replace('/')
        cb()
    }
    const requireDepartment = (nextState, replace, cb) => {
        const { user } = store.getState().auth
        if (!helper.isDepartment(user)) replace('/')
        cb()
    }
    return (
        <Route>
            <Route path={`/${routeName['LOGIN']}`} component={Login} />
            <Route path={`/${routeName['SET_PASSWORD']}(/:uid)(/:token)`} component={SetPassword} />
            {/* Route require login */}
            <Route path="/" component={Layout} onEnter={requireLogin}>
                {/* Declare routes here */}
                <IndexRoute component={Home} />
                <Route path={`/${routeName['TOPIC']}/:id`} component={TopicActivity} />
                <Route path={`/${routeName['BROWSE_LECTURERS']}`} component={BrowseLecturers} />
                <Route path={`/${routeName['BROWSE_LECTURER']}/:id`} component={PublicLecturer} />
                {/* Superuser routes */}
                <Route onEnter={requireSuperuser}>
                    <Route path={`/${routeName['SUPERUSER']}`}>
                        <IndexRedirect to={`/${routeName['NOT_FOUND']}`} />
                        <Route path={`${routeName['FACULTIES']}`} component={SuperuserFaculties} />
                        <Route path={`${routeName['DEGREES']}`} component={SuperuserDegrees} />
                        <Route path={`${routeName['TRAININGS']}`} component={SuperuserTrainings} />
                        <Route path={`${routeName['QUOTAS']}`} component={SuperuserQuotas} />
                    </Route>
                </Route>
                {/* Admin routes */}
                <Route onEnter={requireOfficerAdmin}>
                    <Route path={`/${routeName['ADMIN']}`}>
                        <IndexRedirect to={`/${routeName['NOT_FOUND']}`} />
                        <Route path={`${routeName['DEPARTMENTS']}`} component={AdminDepartments} />
                        <Route path={`${routeName['OFFICERS']}`} component={AdminOfficers} />
                        <Route path={`${routeName['TRAINING_AREAS']}`} component={AdminTrainingAreas} />
                        <Route path={`${routeName['TRAINING_PROGRAMS']}`} component={AdminTrainingPrograms} />
                        <Route path={`${routeName['TRAINING_COURSES']}`} component={AdminTrainingCourses} />
                        <Route path={`${routeName['TRAINING_TYPES_AND_LEVELS']}`} component={AdminTrainingTypesAndLevels} />
                        <Route path={`${routeName['DEGREES']}`} component={AdminDegrees} />
                        <Route path={`${routeName['KNOWLEDGE_AREAS']}`} component={AdminKnowledgeAreas} />
                        <Route path={`${routeName['LEARNERS']}`} component={AdminLearners} />
                        <Route path={`${routeName['TOPICS']}/${routeName['TOPIC_OUT_OFFICERS']}`} component={AdminOutOfficers} />
                        <Route path={`${routeName['TOPICS']}/:type`} component={AdminTopics} />
                        <Route path={`${routeName['DOCUMENTS']}`} component={AdminDocuments} />
                    </Route>
                </Route>
                {/* Lecturer routes */}
                <Route onEnter={requireLecturer}>
                    <Route path={`/${routeName['LECTURER']}`}>
                        <IndexRedirect to={`/${routeName['NOT_FOUND']}`} />
                        <Route path={`${routeName['LECTURER_PROFILE']}`} component={LecturerProfile} />
                        <Route path={`${routeName['LECTURER_TOPIC']}/:type`} component={LecturerCurrentTopics} />
                        <Route path={`${routeName['LECTURER_EXPERTISE']}`} component={LecturerExpertise} />
                    </Route>
                </Route>
                {/* Learner routes */}
                <Route onEnter={requireLearner}>
                    <Route path={`/${routeName['LEARNER']}`}>
                        <IndexRedirect to={`/${routeName['NOT_FOUND']}`} />
                        <Route path={`${routeName['LEARNER_PROFILE']}`} component={LearnerProfile} />
                        <Route path={`${routeName['LEARNER_TOPIC']}`} component={LearnerTopics} />
                    </Route>
                </Route>
                {/* Department routes*/}
                <Route onEnter={requireDepartment}>
                    <Route path={`/${routeName['DEPARTMENT']}`}>
                        <IndexRedirect to={`/${routeName['NOT_FOUND']}`} />
                        <Route path={`${routeName['TOPICS']}`} component={DepartmentTopicExpertise} />
                        <Route path={`${routeName['SEMINAR']}`} component={DepartmentSeminar} />
                    </Route>
                </Route>
                {/* Catch not found routes */}
                <Route path={`/${routeName['NOT_FOUND']}`} component={NotFound} />
                <Route path="*">
                    <IndexRedirect to={`/${routeName['NOT_FOUND']}`} />
                </Route>
            </Route>
        </Route>
    )
}
