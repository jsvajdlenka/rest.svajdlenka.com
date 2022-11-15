CREATE TABLE `game_hedgehog` (
                                 `ID` int(11) NOT NULL,
                                 `MIN_PLAYERS` int(11) NOT NULL,
                                 `MAX_PLAYERS` int(11) NOT NULL,
                                 `CREATOR_ID` int(11) NOT NULL,
                                 `ACTUAL_PLAYER_ID` int(11) NOT NULL,
                                 `ACTUAL_ROUND` int(11) NOT NULL,
                                 `STATE` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Kľúče pre exportované tabuľky
--

--
-- Indexy pre tabuľku `game_hedgehog`
--
ALTER TABLE `game_hedgehog`
    ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT pre exportované tabuľky
--

--
-- AUTO_INCREMENT pre tabuľku `game_hedgehog`
--
ALTER TABLE `game_hedgehog`
    MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Štruktúra tabuľky pre tabuľku `player_hedgehog`
--

CREATE TABLE `player_hedgehog` (
                                   `ID` int(11) NOT NULL,
                                   `GAME_ID` int(11) NOT NULL,
                                   `PLAYER_ID` int(11) NOT NULL,
                                   `PLAYER_NAME` varchar(64) NOT NULL,
                                   `SERVER_ROLL` int(11) NOT NULL,
                                   `CLIENT_ROLL` int(11) NOT NULL,
                                   `CLIENT_ROUND` int(11) NOT NULL,
                                   `GAME_POS` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Kľúče pre exportované tabuľky
--

--
-- Indexy pre tabuľku `player_hedgehog`
--
ALTER TABLE `player_hedgehog`
    ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT pre exportované tabuľky
--

--
-- AUTO_INCREMENT pre tabuľku `player_hedgehog`
--
ALTER TABLE `player_hedgehog`
    MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

COMMIT;
