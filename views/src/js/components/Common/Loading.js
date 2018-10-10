import React, { Component } from 'react'

class Loading extends Component {
    render() {
        return <div class="showbox">
            <div class="loader">
                <svg class="circular" viewBox="25 25 50 50">
                    <circle class="path" cx="50" cy="50" r="20" fill="none" strokeWidth="1" strokeMiterlimit="10"/>
                </svg>
            </div>
        </div>
    }
}

export default Loading
