import{_ as c}from"./AppLayout-BV-VkQcU.js";import l from"./DeleteUserForm-CoWmLCzo.js";import f from"./LogoutOtherBrowserSessionsForm-CycgZUig.js";import{S as s}from"./SectionBorder-MlDbxXV_.js";import u from"./TwoFactorAuthenticationForm-DTlD9chG.js";import d from"./UpdatePasswordForm-DD75uo1M.js";import _ from"./UpdateProfileInformationForm-CUSZfyy0.js";import{c as h,o,w as p,a as i,e as r,f as a,b as t,F as g}from"./app-DsoxI0Qt.js";import"./_plugin-vue_export-helper-DlAUqK2U.js";import"./DialogModal-Bbm1sF06.js";import"./SectionTitle-e3kiCE9B.js";import"./DangerButton-CM5ykoLP.js";import"./TextInput-DJaeW6k6.js";import"./SecondaryButton-BJhbgojE.js";import"./ActionMessage-CGP2HVGv.js";import"./PrimaryButton-B5pnooNg.js";import"./InputLabel-Bn8ZNI2T.js";import"./FormSection-CR8FLvIy.js";const $={class:"max-w-7xl mx-auto py-10 sm:px-6 lg:px-8"},w={key:0},k={key:1},y={key:2},M={__name:"Show",props:{confirmsTwoFactorAuthentication:Boolean,sessions:Array},setup(m){return(e,n)=>(o(),h(c,{title:"Profile"},{header:p(()=>n[0]||(n[0]=[i("h2",{class:"font-semibold text-xl text-gray-800 leading-tight"}," Profile ",-1)])),default:p(()=>[i("div",null,[i("div",$,[e.$page.props.jetstream.canUpdateProfileInformation?(o(),r("div",w,[t(_,{user:e.$page.props.auth.user},null,8,["user"]),t(s)])):a("",!0),e.$page.props.jetstream.canUpdatePassword?(o(),r("div",k,[t(d,{class:"mt-10 sm:mt-0"}),t(s)])):a("",!0),e.$page.props.jetstream.canManageTwoFactorAuthentication?(o(),r("div",y,[t(u,{"requires-confirmation":m.confirmsTwoFactorAuthentication,class:"mt-10 sm:mt-0"},null,8,["requires-confirmation"]),t(s)])):a("",!0),t(f,{sessions:m.sessions,class:"mt-10 sm:mt-0"},null,8,["sessions"]),e.$page.props.jetstream.hasAccountDeletionFeatures?(o(),r(g,{key:3},[t(s),t(l,{class:"mt-10 sm:mt-0"})],64)):a("",!0)])])]),_:1}))}};export{M as default};
