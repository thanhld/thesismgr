import React, { Component } from 'react'
import ReactDOM from 'react-dom'

let notificationWrapperId = 'notification-wrapper'
let defaultTimeout = 2200 // ms
let animationDuration = 300 // ms

/* React Notification Component */
class Toast extends Component {
    state = {
        status: "show"
    }

    getVisibleState(context) {
        setTimeout(() => {
            context.updateStatus("show")
        })
        if (this.props.timeout === -1) {
            return
        }
        setTimeout(() => {
            context.updateStatus("hide")
        }, this.props.timeout)
    }

    updateStatus(status) {
        this.setState({
            status
        })
    }

    componentDidMount() {
        this.getVisibleState(this)
    }
	render() {
		let { text, type } = this.props
        let { status } = this.state
        let containerClass = `toast-notification-${type}`
        if (status == "show") containerClass += " toast-fadein"
        else if (status == "hide") containerClass += " toast-fadeout"
		return (
			<div class={containerClass}>
                <span class="toast-icon">
                    {
                        type == "primary" ? <i class="fa fa-info fa-lg" aria-hidden="true"></i>
                            : type == "success" ? <i class="fa fa-check fa-lg" aria-hidden="true"></i>
                        : <i class="fa fa-times fa-lg" aria-hidden="true"></i>
                    }

                </span>
				<span>{text}</span>
			</div>
		)
	}
}

/* Private Functions */

/* Render React component */
function renderToast(text, type, timeout) {
	ReactDOM.render(
		<Toast text={text} timeout={timeout} type={type} />,
		document.getElementById(notificationWrapperId)
	)
}

/* Unmount React component */
function hideToast() {
	ReactDOM.unmountComponentAtNode(document.getElementById(notificationWrapperId))
}

/* Public functions */

/* Show Animated Toast Message */
function show(text, type, timeout) {
	if (!document.getElementById(notificationWrapperId).hasChildNodes()) {
		let renderTimeout = timeout

		// Use default timeout if not set.
		if (!renderTimeout) {
			renderTimeout = defaultTimeout
		}

		// Render Component with Props.
		renderToast(text, type, renderTimeout)

		if (timeout === -1) {
			return
		}

		// Unmount react component after the animation finished.
		setTimeout(function() {
			hideToast()
		}, renderTimeout + animationDuration)
	}
}


/* Export notification container */
export default class extends Component {
	render() {
		return (
			<div id={notificationWrapperId}></div>
		)
	}
}

/* Export notification functions */
export let notify = {
	show
}
