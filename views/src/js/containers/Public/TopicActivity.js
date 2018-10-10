import React, { Component } from 'react'
import { connect } from 'react-redux'
import { bindActionCreators } from 'redux'
import { TopicActivity } from 'Components'
import { sharedActions } from 'Actions'

const mapStateToProps = state => {
    return {
        uid: state.auth.user.uid,
        degrees: state.degrees,
        lecturers: state.lecturers,
        outOfficers: state.outOfficers
    }
}

const mapDispatchToProps = dispatch => {
    const { loadLecturers, loadOutOfficers, loadDegrees } = sharedActions
    return {
        actions: bindActionCreators({loadLecturers, loadOutOfficers, loadDegrees}, dispatch)
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(TopicActivity)
