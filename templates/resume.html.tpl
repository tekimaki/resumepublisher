<html>
	<head>
		<title>{$config.profile.first_name} {$config.profile.last_name}</title>
	</head>
	<body>
		<div>
		<table class="header">
			<tr>
				<td>
					<h1>{$config.profile.first_name} {$config.profile.last_name}</h1>
					<div class="contact">
						email: <a href="mailto:{$config.profile.email}">{$config.profile.email}</a><br />
						phone: {$config.profile.phone}<br />
						{if $config.aim}aim: {$config.aim}<br />{/if}
						{if $config.profile.address.city}location: {$config.profile.address.city}{/if}
					</div>
				</td>
			</tr>
			<tr>
				<th colspan=2 class="myborder">
					<strong>Summary</strong>
				</th>
			</tr>
			<tr>
				<td>
					<p>{$config.summary}</p>
				</td>
			</tr>
			<tr>
				<td>
					<h4>Skills</h4>
				</td>
				{foreach from=$config.skills item=skill}
				<td>
					<p>
						<strong>{$skill.label|escape}:</strong>
						{$skill.description}
					</p>
				</td>
				{/foreach}
			</tr>
		</table>
		<table class="employment">
			<tr>
				<th colspan=2>
					<strong>Career History</strong>
				</th>
			</tr>
			{foreach from=$config.employment item=job}
			<tr>
				<td>
					<strong>{$job.title|escape}</strong><br/>
					{if $job.employer.url}<a href="{$job.employer.url}">{/if}{$job.employer.name}{if $job.employer.url}</a>{/if}<br/>
					{$job.start_date} - {$job.end_date}
				</td>
				<td>
					<p><em>{$job.summary}</em></p>
					<ul>
						{foreach from=$job.highlights item=highlight}
						<li>{$highlight}</li>
						{/foreach}
					</ul>
				</td>
			</tr>
			{/foreach}
		</table>
		<table class="education">
			<tr>
				<th colspan=2>
					<strong>Education</strong>
				</th>
			</tr>
			{foreach from=$config.education item=program}
			<tr>
				<td>
					<strong>{$program.school_name}</strong><br/>
					{$program.start_date} - {$program.end_date}
				</td>
				<td>
					<p>{$program.summary}</p>
				</td>
			</tr>
			{/foreach}
		</table>
	</div>
	</body>
</html>
