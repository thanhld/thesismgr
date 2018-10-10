import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import { LecturerCurrentTopics } from 'Components'
import { lecturerActions, sharedActions } from 'Actions'

const mapStateToProps = state => {
    return {
        uid: state.auth.user.uid,
        topics: state.lecturerTopics,
        lecturers: state.lecturers,
        degrees: state.degrees,
        outOfficers: state.outOfficers,
        profile: state.lecturerProfile,
        quotas: state.quotas
    }
}

const mapDispatchToProps = dispatch => {
    const { loadTopics, loadLecturerInformation } = lecturerActions
    const { loadLecturers, loadDegrees, loadOutOfficers, createActivity, loadActiveQuota } = sharedActions
    return { actions: bindActionCreators({loadTopics, loadLecturerInformation, loadLecturers, loadDegrees, loadOutOfficers, createActivity, loadActiveQuota}, dispatch) }
}

export default connect(mapStateToProps, mapDispatchToProps)(LecturerCurrentTopics)
