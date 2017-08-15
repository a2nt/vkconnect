<% if CurrentVkMember %>
	<% with CurrentVkMember %>
		<p>Welcome back $FirstName</p>
		<% include VkConnectLogout %>

		<img src="$Avatar(square)" alt="$FirstName" />
	<% end_with %>
<% end_if %>
