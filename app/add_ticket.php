<?php
/**
 * bugspray issue tracking software
 * Copyright (c) 2009-2010 a2h - http://a2h.uni.cc/
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * Under section 7b of the GNU General Public License you are
 * required to preserve this notice. Additional attribution may be
 * found in the NOTICES.txt file provided with the Program.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

// if possible make this more like the reg/login forms or at least introduce similarities to this or them

include("functions.php");
$page->setType('tickets');
$page->setTitle('Add a ticket');
?>

<div class="imgtitle imgtitle-32">
	<img class="image" src="<?php echo $location['images']; ?>/titles/tickets-add.png" alt="" />
	<div class="text">
		<h1>Add a ticket</h1>
	</div>
	<div class="clear"></div>
</div>

<?php
if (!$client['is_logged'])
{
	echo 'You are not logged in.';
}
else
{

if (isset($_POST['submit']))
{
	$title = escape_smart(htmlentities($_POST['title']));
	$description = escape_smart(htmlentities($_POST['description']));
	$severity = escape_smart($_POST['severity']);
	
	$error = false;
	$errors_title = array();
	$errors_tags = array();
	$errors_description = array();
	
	$tags = escape_smart(htmlentities($_POST['tags'])); // todo: use the separate table for tags instead of one long string
	$tagsarr = explode(' ', $tags);
	sort($tagsarr);
	$tagsc = count($tagsarr);
	
	if ($tagsc > 5)
	{
		$error = true;
		$errors_tags[] = 'You may only provide up to 5 tags.';
	}
	
	if ($tagsc > 0) // we still want to run this even if there's more than 5 tags to check for other errors
	{
		$tags = '';
		for ($i=0; $i<$tagsc; $i++)
		{
			if (strstr($tags, $tagsarr[$i]))
			{
				$error = true;
				$errors_tags[] = 'The tag you entered \'' . $tagsarr[$i] . '\' has been entered more than once.';
			}
			elseif (strlen($tagsarr[$i]) > 16) // todo: client side check for this
			{
				$error = true;
				$errors_tags[] = 'The tag you entered \'' . $tagsarr[$i] . '\' exceeds the maximum length of tags of 16 characters.';
			}
			else
			{
				$tags .= ($i > 0 ? ' ' : '') . $tagsarr[$i];
			}
		}
	}
	
	if (!hascharacters($title))
	{
		$error = true;
		$errors_title[] = 'The summary you provided for your ticket is blank.';
	}
	if (!hascharacters($description))
	{
		$error = true;
		$errors_description[] = 'The description you provided for your ticket is blank.';
	}
	
	if (!$error)
	{
		$query2 = db_query("
			INSERT INTO issues (name, author, description, category, when_opened, when_updated, tags, severity)
			VALUES ('$title', {$_SESSION['uid']}, '$description', '1', NOW(), NOW(), '$tags', '$severity')
		");
		
		if ($query2) { echo '<p><b>Info:</b> Added issue successfully!</p>'; } else { echo mysql_error(); }
		
		$query2_id = mysql_insert_id();
		
		echo '<br />';
		
		$query3 = db_query("INSERT INTO log_issues (when_occured,userid,actiontype,issue) VALUES (NOW(), {$_SESSION['uid']}, 1, $query2_id)");
		if ($query3) { echo '<p><b>Info:</b> Logged successfully!</p>'; } else { mysql_error(); }
		
		echo '<p><a href="ticket.php?id=' . $query2_id . '">Go to issue</a></p>';
	}
}

if (!isset($error) || $error)
{
?>

<form action="" method="post">
	
	<?php echo output_errors($errors_title) ?>
	
	<dl class="form inline">
		<dt>
			<label for="title">Summary</label>
		</dt>
		<dd>
			<input id="title" name="title" type="text" size="64" maxlength="128" value="<?php echo $_POST['title'] ?>" />
		</dd>
	</dl>
	
	<div class="clear"></div>
	
	<?php echo output_errors($errors_tags) ?>
	
	<dl class="form inline">
		<dt>
			<label for="tags">Tags</label>
		</dt>
		<dd>
			<input id="tags" name="tags" type="text" size="64" value="<?php echo $_POST['tags'] ?>" />
			<small>(seperate tags by spaces)</small>
		</dd>
	</dl>
	
	<div class="clear"></div>
	
	<?php echo output_errors($errors_description) ?>
	
	<dl class="form">
		<dt>
			<label for="description">Describe the problem</label>
		</dt>
		<dd>
			<textarea id="description" name="description" style="height: 192px;"><?php echo $_POST['description'] ?></textarea>
		</dd>
	</dl>
	
	<dl class="form col-3">
		<dt>
			<label for="severity">Severity</label>
		</dt>
		<dd>
			<select id="severity" name="severity">
				<option value="0">None</option>
				<option value="1">Very Low</option>
				<option value="2">Low</option>
				<option value="3">Medium</option>
				<option value="4">Severe</option>
				<option value="5">Very Severe</option>
			</select>
		</dd>
	</dl>
	
	<dl class="form col-3">
		<dt>
			<label>--</label>
		</dt>
		<dd>
			--
		</dd>
	</dl>
	
	<dl class="form col-3">
		<dt>
			<label>--</label>
		</dt>
		<dd>
			--
		</dd>
	</dl>
	
	<div class="clear"></div>

	<input type="submit" name="submit" value="Post" />
</form>

<?php
}

}
?>