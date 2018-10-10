import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import { LecturerExpertise } from 'Components'
import { lecturerActions, sharedActions } from 'Actions'

const mapStateToProps = state => {
    return {
        uid: state.auth.user.uid,
        topics: state.lecturerTopics,
        lecturers: state.lecturers,
        degrees: state.degrees,
        outOfficers: state.outOfficers
    }
}

const mapDispatchToProps = dispatch => {
    const { loadTopics, updateReview } = lecturerActions
    const { loadLecturers, loadDegrees, loadOutOfficers } = sharedActions
    return { actions: bindActionCreators({loadTopics, updateReview, loadLecturers, loadDegrees, loadOutOfficers}, dispatch) }
}

export default connect(mapStateToProps, mapDispatchToProps)(LecturerExpertise)
