import React, { Component } from 'react'
import * as helper from 'Helper'
import SidebarAdminInfo from './SidebarAdminInfo'
import SidebarAdminThesis from './SidebarAdminThesis'
import SidebarLecturer from './SidebarLecturer'
import SidebarPublic from './SidebarPublic'
import SidebarSuperuser from './SidebarSuperuser'
import SidebarLearner from './SidebarLearner'
import SidebarDepartment from './SidebarDepartment'

class Sidebar extends Component {
    constructor(props) {
        super(props)
        this.openLeftSlidebar = this.openLeftSlidebar.bind(this);
        this.closeLeftSlidebar = this.closeLeftSlidebar.bind(this);
        this.windowResizeHandler = this.windowResizeHandler.bind(this);
    }

    componentDidMount() {
        window.addEventListener('resize',this.windowResizeHandler);
    }

    windowResizeHandler() {
        $(".home-right-fading").fadeOut(0);
        if ($(window).width() > 768) {
            this.closeLeftSlidebar();
        }
    }

    componentWillUnmount() {
        window.removeEventListener('resize',this.windowResizeHandler);
        this.closeLeftSlidebar();
    }

    openLeftSlidebar() {
        $("#sidebar").parent().addClass("left-slide-bar--shown");
        $(".home-right-fading").css('visibility','visible');
        $(".home-right-fading").fadeIn(250);
    }

    closeLeftSlidebar() {
        $(".home-right-fading").fadeOut(250);
        $("#sidebar").parent().removeClass("left-slide-bar--shown");
    }

    render() {
        const { user, topics, adminOutTopics, sidebar } = this.props
        return (
            <div>
                <div class="home-right-fading close-left-navigation" onClick = {this.closeLeftSlidebar}></div>

                <div class="button-toggle-slidebar" onClick={this.openLeftSlidebar}>
                    <i class="fa fa-bars" aria-hidden="true"></i>
                </div>

                <div id="sidebar" class="left-slide-bar--desktop">
                    <div class="sidebar-nav">
                        { !helper.isSuperuser(user) && <SidebarPublic closebar={this.closeLeftSlidebar} /> }
                        { helper.isSuperuser(user) && <SidebarSuperuser closebar={this.closeLeftSlidebar} /> }
                        { helper.isOfficerAdmin(user) && <SidebarAdminInfo closebar={this.closeLeftSlidebar} /> }
                        { helper.isLecturer(user) && <SidebarLecturer user={user} topics={topics} closebar={this.closeLeftSlidebar} /> }
                        { helper.isLearner(user) && <SidebarLearner user={user} closebar={this.closeLeftSlidebar} /> }
                        { helper.isDepartment(user) && <SidebarDepartment closebar={this.closeLeftSlidebar} /> }
                        { (helper.isOfficerAdmin(user)) && <SidebarAdminThesis topics={adminOutTopics} closebar={this.closeLeftSlidebar} /> }
                    </div>
                </div>
            </div>
        )
    }
}

export default Sidebar
